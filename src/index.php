<?php



session_start();


if (!isset($_SESSION['products'])) {
    $_SESSION['products'] = [
        ["id" => 1, "name" => "Laptop", "description" => "Dell XPS 13", "price" => 1200, "category" => "Electronics"],
        ["id" => 2, "name" => "Shoes", "description" => "Nike Air Zoom", "price" => 150, "category" => "Fashion"]
    ];
}

// مرجع للمصفوفة داخل الجلسة
$products = &$_SESSION['products'];

$errors = [];
$submittedData = [];

// عرض رسالة النجاح من الـ Session إن وجدت
$successMessage = "";
if (isset($_SESSION['success'])) {
    $successMessage = $_SESSION['success'];
    unset($_SESSION['success']); // تم العرض مرة واحدة فقط
}

// معالجة النموذج
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedData = $_POST;

    // Sanitization
    $name = trim(htmlspecialchars($_POST['name'] ?? ""));
    $description = trim(htmlspecialchars($_POST['description'] ?? ""));
    $price = trim($_POST['price'] ?? "");
    $category = trim($_POST['category'] ?? "");

    // Validation
    if ($name === "") $errors['name'] = "Product name is required";
    if ($description === "") $errors['description'] = "Description is required";
    if (!is_numeric($price) || $price <= 0) $errors['price'] = "Price must be a positive number";
    if ($category === "") $errors['category'] = "Category is required";

    // إذا لم توجد أخطاء
    if (empty($errors)) {
        $newId = count($products) + 1;
        $products[] = [
            "id" => $newId,
            "name" => $name,
            "description" => $description,
            "price" => $price,
            "category" => $category
        ];
        $_SESSION['success'] = "Product added successfully!";
        $submittedData = []; // تفريغ الحقول

        // إعادة التوجيه لتفادي إعادة الإرسال عند عمل Refresh
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="container mt-4">

    <!-- رسائل النجاح والأخطاء -->
    <?php if ($successMessage): ?>
        <div class="alert alert-success"><?= $successMessage ?></div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">Please fix the errors below.</div>
    <?php endif; ?>

    <!-- جدول المنتجات -->
    <h2>Product List</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th><th>Name</th><th>Description</th><th>Price</th><th>Category</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['id']) ?></td>
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td><?= htmlspecialchars($p['description']) ?></td>
                    <td>$<?= number_format($p['price'], 2) ?></td>
                    <td><?= htmlspecialchars($p['category']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- نموذج إضافة منتج -->
    <h2 class="mt-4">Add New Product</h2>
    <form method="post" class="mt-3">
        <div class="mb-3">
            <label class="form-label">Product Name</label>
            <input type="text" name="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" value="<?= $submittedData['name'] ?? '' ?>">
            <div class="invalid-feedback"><?= $errors['name'] ?? '' ?></div>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>"><?= $submittedData['description'] ?? '' ?></textarea>
            <div class="invalid-feedback"><?= $errors['description'] ?? '' ?></div>
        </div>

        <div class="mb-3">
            <label class="form-label">Price</label>
            <input type="number" name="price" class="form-control <?= isset($errors['price']) ? 'is-invalid' : '' ?>" value="<?= $submittedData['price'] ?? '' ?>">
            <div class="invalid-feedback"><?= $errors['price'] ?? '' ?></div>
        </div>

        <div class="mb-3">
            <label class="form-label">Category</label>
            <select name="category" class="form-select <?= isset($errors['category']) ? 'is-invalid' : '' ?>">
                <option value="">--Select--</option>
                <option value="Electronics" <?= (isset($submittedData['category']) && $submittedData['category']=="Electronics") ? 'selected' : '' ?>>Electronics</option>
                <option value="Fashion" <?= (isset($submittedData['category']) && $submittedData['category']=="Fashion") ? 'selected' : '' ?>>Fashion</option>
                <option value="Books" <?= (isset($submittedData['category']) && $submittedData['category']=="Books") ? 'selected' : '' ?>>Books</option>
            </select>
            <div class="invalid-feedback"><?= $errors['category'] ?? '' ?></div>
        </div>

        <button type="submit" class="btn btn-primary">Add Product</button>
    </form>

</body>
</html>