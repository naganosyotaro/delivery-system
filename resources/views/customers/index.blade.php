<x-app-layout>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">顧客一覧</h1>
        <a href="{{ route('customers.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> 新規登録
        </a>
    </div>

    <!-- 検索 -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('customers.index') }}" method="GET">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" 
                           value="{{ request('search') }}" placeholder="会社名・担当者名で検索">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 顧客一覧 -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>会社名</th>
                            <th>担当者</th>
                            <th>電話番号</th>
                            <th>発送件数</th>
                            <th class="text-end">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                        <tr>
                            <td>
                                <a href="{{ route('customers.show', $customer) }}" class="text-decoration-none fw-medium">
                                    {{ $customer->company_name }}
                                </a>
                            </td>
                            <td>{{ $customer->contact_name }}</td>
                            <td>{{ $customer->phone ?: '-' }}</td>
                            <td>{{ $customer->shipments_count }}件</td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('customers.show', $customer) }}" class="btn btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('customers.edit', $customer) }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                顧客データがありません
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($customers->hasPages())
        <div class="card-footer">
            {{ $customers->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
