<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Call History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8fafc;
        }
        .header-bar {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.06);
            padding: 2rem 2rem 1.5rem 2rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .header-title {
            font-size: 2.2rem;
            font-weight: 700;
            margin: 0;
        }
        .header-actions {
            display: flex;
            gap: 0.75rem;
        }
        .call-status {
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
        }
        .call-status.initiated {
            background-color: #fff3cd;
            color: #856404;
        }
        .call-status.completed {
            background-color: #d1edff;
            color: #0c5460;
        }
        .call-status.failed {
            background-color: #f8d7da;
            color: #721c24;
        }
        .filters-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .table-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
            padding: 1.5rem;
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        @media (max-width: 600px) {
            .header-bar {
                flex-direction: column;
                align-items: flex-start;
                padding: 1.2rem 1rem 1rem 1rem;
            }
            .header-actions {
                margin-top: 1rem;
                width: 100%;
                justify-content: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="header-bar">
            <div class="header-title">
                <i class="fas fa-phone-alt me-2"></i>
                Call History
            </div>
            <div class="header-actions">
                <a href="{{ route('lead-clients.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-1"></i>
                    Back to Leads
                </a>
                <form action="{{ url('/logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="fas fa-sign-out-alt me-1"></i>
                        Logout
                    </button>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">{{ $callHistory->total() }}</h4>
                            <small>Total Calls</small>
                        </div>
                        <i class="fas fa-phone fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">{{ $callHistory->where('call_status', 'initiated')->count() }}</h4>
                            <small>Initiated</small>
                        </div>
                        <i class="fas fa-clock fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">{{ $callHistory->where('call_status', 'completed')->count() }}</h4>
                            <small>Completed</small>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">{{ $callHistory->where('call_status', 'failed')->count() }}</h4>
                            <small>Failed</small>
                        </div>
                        <i class="fas fa-times-circle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call History Table -->
        <div class="table-card">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>
                                <i class="fas fa-phone me-1"></i>
                                Phone Number
                            </th>
                            <th>
                                <i class="fas fa-envelope me-1"></i>
                                Email
                            </th>
                            <th>
                                <i class="fas fa-user me-1"></i>
                                Lead Client
                            </th>
                            <th>
                                <i class="fas fa-info-circle me-1"></i>
                                Status
                            </th>
                            <th>
                                <i class="fas fa-calendar me-1"></i>
                                Call Time
                            </th>
                            <th>
                                <i class="fas fa-id-badge me-1"></i>
                                Call SID
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($callHistory as $call)
                            <tr>
                                <td>
                                    <i class="fas fa-phone text-muted me-1"></i>
                                    {{ $call->phone_number }}
                                </td>
                                <td>
                                    @if($call->email)
                                        <i class="fas fa-envelope text-muted me-1"></i>
                                        {{ $call->email }}
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($call->leadClient)
                                        <i class="fas fa-user text-muted me-1"></i>
                                        {{ $call->leadClient->name }}
                                        <small class="text-muted d-block">ID: {{ $call->leadClient->id }}</small>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="call-status {{ $call->call_status }}">
                                        @if($call->call_status == 'initiated')
                                            <i class="fas fa-clock me-1"></i>
                                        @elseif($call->call_status == 'completed')
                                            <i class="fas fa-check me-1"></i>
                                        @elseif($call->call_status == 'failed')
                                            <i class="fas fa-times me-1"></i>
                                        @endif
                                        {{ ucfirst($call->call_status) }}
                                    </span>
                                </td>
                                <td>
                                    <i class="fas fa-calendar text-muted me-1"></i>
                                    {{ $call->call_timestamp->format('Y-m-d H:i:s') }}
                                    <small class="text-muted d-block">{{ $call->call_timestamp->diffForHumans() }}</small>
                                </td>
                                <td>
                                    @if($call->call_sid)
                                        <code class="small">{{ $call->call_sid }}</code>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No call history found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {!! $callHistory->links() !!}
            </div>
        </div>
    </div>

    <!-- Call Details Modal -->
    <div class="modal fade" id="callDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-phone me-2"></i>
                        Call Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="callDetailsContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewCallDetails(callId) {
            // This would typically make an AJAX call to get detailed call information
            document.getElementById('callDetailsContent').innerHTML = 
                '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2">Loading call details...</p></div>';
            
            // You can implement AJAX here to fetch call details
            // fetch('/api/call-history/' + callId)
            //     .then(response => response.json())
            //     .then(data => {
            //         // Display the call details
            //     });
            
            new bootstrap.Modal(document.getElementById('callDetailsModal')).show();
        }
    </script>
</body>
</html> 