<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Form Login</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background-color: #f8f9fa;
    }
    .form-container {
      width: 100%;
      max-width: 400px;
      background: white;
      border-radius: 15px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      padding: 2rem;
    }
  </style>
</head>
<body>

  <div class="form-container">
    <h4 class="text-center mb-4">Make an Account</h4>
    <form action="<?= base_url('master/proccess/').'?failed=false' ?> " method="post">
      <div class="mb-3">
        <label for="secretId" class="form-label">Secret ID</label>
        <input type="text" class="form-control" id="secretId" name="id" placeholder="Masukkan Secret ID">
      </div>

      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan Email">
      </div>

      <div class="mb-4">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan Password">
      </div>

      <div class="d-grid">
        <button type="submit" class="btn btn-primary">Masuk</button>
      </div>
    </form>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
