<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mouvements de Stock</title>
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
                        <a class="nav-link active" href="{{ route('stock-movements.index') }}">
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-exchange-alt me-2"></i>Mouvements de Stock</h1>
            <div class="btn-group" role="group">
                <a href="{{ route('stock-movements.quick-entry') }}" class="btn btn-success">
                    <i class="fas fa-plus me-2"></i>Entrée Rapide
                </a>
                <a href="{{ route('stock-movements.quick-exit') }}" class="btn btn-danger">
                    <i class="fas fa-minus me-2"></i>Sortie Rapide
                </a>
                <a href="{{ route('stock-movements.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Nouveau Mouvement
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-history me-2"></i>Historique des Mouvements</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Produit</th>
                                <th>Type</th>
                                <th>Quantité</th>
                                <th>Avant</th>
                                <th>Après</th>
                                <th>Raison</th>
                                <th>Utilisateur</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($movements as $movement)
                            <tr>
                                <td>{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <strong>{{ $movement->product->name }}</strong>
                                    @if($movement->product->category)
                                        <br><small class="badge" style="background-color: {{ $movement->product->category->color }}; color: white;">
                                            {{ $movement->product->category->name }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $movement->type_color }}">
                                        <i class="{{ $movement->type_icon }} me-1"></i>{{ $movement->type_label }}
                                    </span>
                                </td>
                                <td>
                                    <strong>{{ $movement->quantity }}</strong>
                                </td>
                                <td>{{ $movement->quantity_before }}</td>
                                <td>{{ $movement->quantity_after }}</td>
                                <td>{{ $movement->reason ?: '-' }}</td>
                                <td>{{ $movement->user->name }}</td>
                                <td>
                                    <a href="{{ route('stock-movements.show', $movement->id) }}" class="btn btn-sm btn-outline-primary" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="fas fa-exchange-alt fa-2x text-muted mb-3"></i>
                                    <p class="text-muted">Aucun mouvement de stock enregistré</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($movements->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $movements->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 