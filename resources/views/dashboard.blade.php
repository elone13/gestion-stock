<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Gestion de Stock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="fas fa-boxes me-2"></i>Gestion de Stock
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('products.index') }}">
                            <i class="fas fa-box me-1"></i>Produits
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('categories.index') }}">
                            <i class="fas fa-tags me-1"></i>Catégories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('stock-movements.index') }}">
                            <i class="fas fa-exchange-alt me-1"></i>Mouvements
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('notifications.index') }}">
                            <i class="fas fa-bell me-1"></i>Notifications
                            @if(auth()->user()->unreadNotificationsCount() > 0)
                                <span class="badge bg-danger ms-1">{{ auth()->user()->unreadNotificationsCount() }}</span>
                            @endif
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i>{{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="fas fa-user-edit me-1"></i>Profil
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt me-1"></i>Déconnexion
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </h1>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="card-title">Total Produits</h5>
                                <h2 class="mb-0">{{ \App\Models\Product::count() }}</h2>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-boxes fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="card-title">En Stock</h5>
                                <h2 class="mb-0">{{ \App\Models\Product::where('quantity', '>', 0)->count() }}</h2>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-check-circle fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="card-title">Rupture</h5>
                                <h2 class="mb-0">{{ \App\Models\Product::where('quantity', '=', 0)->count() }}</h2>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-exclamation-triangle fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-cogs me-2"></i>Actions Rapides
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <a href="{{ route('products.create') }}" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-plus me-2"></i>Ajouter un Produit
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="{{ route('products.index') }}" class="btn btn-info btn-lg w-100">
                                    <i class="fas fa-list me-2"></i>Voir tous les Produits
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="{{ route('categories.index') }}" class="btn btn-success btn-lg w-100">
                                    <i class="fas fa-tags me-2"></i>Gérer les Catégories
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="{{ route('stock-movements.index') }}" class="btn btn-warning btn-lg w-100">
                                    <i class="fas fa-exchange-alt me-2"></i>Mouvements de Stock
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(\App\Models\Product::count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>Produits en Rupture de Stock
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Prix</th>
                                        <th>Quantité</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(\App\Models\Product::where('quantity', '=', 0)->take(5)->get() as $product)
                                    <tr>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->price }} FCFA</td>
                                        <td><span class="badge bg-danger">{{ $product->quantity }}</span></td>
                                        <td>
                                            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit me-1"></i>Modifier
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
