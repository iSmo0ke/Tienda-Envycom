@extends('admin.layouts.admin')

@section('content')
<div class="container-fluid px-0">
    <div class="page-header mb-4" style="background: linear-gradient(135deg, var(--envy-blue) 0%, #1a4b8c 100%); padding: 30px; border-radius: 16px; color: white;">
        <h1 class="fw-bold m-0 fs-3">Directorio de Clientes</h1>
        <p class="mt-2 mb-0" style="color: #cbd5e1;">Base de datos de usuarios registrados en la tienda.</p>
    </div>

    <div class="table-card" style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.04);">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr style="text-transform: uppercase; font-size: 0.75rem; color: #64748b; letter-spacing: 1px;">
                        <th>Cliente</th>
                        <th>Correo Electrónico</th>
                        <th>Fecha de Registro</th>
                        <th class="text-center">Total de Pedidos</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-light rounded-circle d-flex justify-content-center align-items-center text-secondary fw-bold" style="width: 40px; height: 40px;">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <span class="fw-bold text-dark">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="text-muted">{{ $user->email }}</td>
                            <td>{{ $user->created_at->format('d/m/Y') }}</td>
                            <td class="text-center">
                                <span class="badge bg-secondary rounded-pill px-3">{{ $user->orders_count }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">No hay clientes registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end mt-3">{{ $users->links('pagination::bootstrap-5') }}</div>
    </div>
</div>
@endsection