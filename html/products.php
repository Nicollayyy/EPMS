<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>1028 Tea & Café — Products</title>

  <!-- Bootstrap & FontAwesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="./../css/dashboard.css">
</head>
<body class="dash-body">

  <!-- Include Topbar -->
  <?php include './../Includes/topbar.php'; ?>

  <div class="app-shell d-flex">

    <!-- Include Sidebar -->
    <?php include './../Includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="content p-4">

      <?php
      // Display success messages
      if (isset($_GET['success'])) {
          $message = '';
          if ($_GET['success'] == '1') {
              $message = 'Product added successfully!';
          } elseif ($_GET['success'] == 'deleted') {
              $message = 'Product deleted successfully!';
          } elseif ($_GET['success'] == 'updated') {
              $message = 'Product updated successfully!';
          }
          if ($message) {
              echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                      <i class="fa-solid fa-circle-check me-2"></i>' . $message . '
                      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>';
          }
      }
      ?>

      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h2 class="mb-1" style="color: #ffffff;">Product Menu</h2>
          <p class="mb-0" style="color: #ffffff; opacity: 0.9;">Manage your café menu items by category</p>
        </div>
        <a href="./../product_add.php" class="btn btn-gold">
          <i class="fa-solid fa-plus me-2"></i>Add Product
        </a>
      </div>

      <!-- Search Bar -->
      <div class="panel p-3 mb-4">
        <div class="input-group">
          <span class="input-group-text bg-white border-end-0">
            <i class="fa-solid fa-search text-muted"></i>
          </span>
          <input type="text" id="searchInput" class="form-control border-start-0 ps-0" placeholder="Search products by name...">
        </div>
      </div>

      <!-- Products by Category -->
      <div id="productsContainer" class="row g-4">
        <!-- Products will be loaded here -->
      </div>

      <footer class="mt-4 text-center text-muted small">
        © 1028 Tea & Café — Expense & Profit Management
      </footer>
    </main>

  </div>

  <!-- Bootstrap bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Page JS -->
  <script>
    // Sidebar toggle
    const btnToggle = document.getElementById('btnToggle');
    const sidebar = document.getElementById('sidebar');
    btnToggle?.addEventListener('click', () => sidebar.classList.toggle('collapsed'));

    // Profile dropdown
    const profileToggle = document.getElementById('profileToggle');
    const profileMenu = document.getElementById('profileMenu');
    profileToggle?.addEventListener('click', (e) => {
      e.stopPropagation();
      profileMenu.classList.toggle('show');
    });
    document.addEventListener('click', () => profileMenu.classList.remove('show'));

    // Store all products data
    let allProductsData = {};

    // Fetch and display products
    async function loadProducts(searchTerm = '') {
      try {
        const response = await fetch('../fetch_products.php');
        const data = await response.json();

        // Store the data for filtering
        allProductsData = data.categories || {};

        const container = document.getElementById('productsContainer');
        
        if (data.categories && Object.keys(data.categories).length > 0) {
          container.innerHTML = '';
          
          // Filter products based on search term
          let hasResults = false;
          
          // Loop through each category
          for (const [category, products] of Object.entries(data.categories)) {
            // Filter products by search term
            const filteredProducts = searchTerm 
              ? products.filter(product => 
                  product.name.toLowerCase().includes(searchTerm.toLowerCase())
                )
              : products;

            // Only show category if it has matching products
            if (filteredProducts.length > 0) {
              hasResults = true;
              const categoryCard = `
                <div class="col-12 col-md-6 col-lg-4">
                  <div class="panel p-4">
                    <div class="panel-head mb-3">
                      <h5 class="mb-1">
                        <i class="fa-solid fa-mug-hot me-2 text-warning"></i>${category}
                      </h5>
                      <small class="text-muted">${filteredProducts.length} item${filteredProducts.length !== 1 ? 's' : ''}</small>
                    </div>
                    <div class="product-list">
                      ${filteredProducts.map(product => `
                        <div class="product-item d-flex justify-content-between align-items-center mb-3 p-3">
                          <div>
                            <div class="fw-semibold">${product.name}</div>
                            <div class="text-muted small">${product.size}</div>
                            <div class="text-success fw-bold">₱ ${parseFloat(product.price).toFixed(2)}</div>
                          </div>
                          <div class="btn-group">
                            <a href="../product_edit.php?id=${product.product_id}" class="btn btn-sm btn-warning">Edit</a>
                            <a href="../product_delete.php?id=${product.product_id}" class="btn btn-sm btn-danger" onclick="return confirm('Delete this product?')">Delete</a>
                          </div>
                        </div>
                      `).join('')}
                    </div>
                  </div>
                </div>
              `;
              container.innerHTML += categoryCard;
            }
          }

          // Show no results message if search returned nothing
          if (!hasResults && searchTerm) {
            container.innerHTML = `
              <div class="col-12">
                <div class="panel p-5 text-center">
                  <i class="fa-solid fa-search fa-3x text-muted mb-3"></i>
                  <h5 class="text-muted">No products found</h5>
                  <p class="text-muted mb-0">Try searching with different keywords</p>
                </div>
              </div>
            `;
          }
        } else {
          container.innerHTML = `
            <div class="col-12">
              <div class="panel p-5 text-center">
                <i class="fa-solid fa-box-open fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No products yet</h5>
                <p class="text-muted mb-3">Start by adding your first product</p>
                <a href="../product_add.php" class="btn btn-gold">
                  <i class="fa-solid fa-plus me-2"></i>Add Product
                </a>
              </div>
            </div>
          `;
        }

      } catch (error) {
        console.error('Error loading products:', error);
      }
    }

    // Search functionality
    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('input', (e) => {
      loadProducts(e.target.value);
    });

    // Load products on page load
    loadProducts();
  </script>
</body>
</html>
