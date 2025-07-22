<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lead Clients</title>
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
        .nav-tabs .nav-link {
            color: #6c757d;
            border: none;
            border-bottom: 2px solid transparent;
        }
        .nav-tabs .nav-link.active {
            color: #0d6efd;
            border-bottom: 2px solid #0d6efd;
            background: none;
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
        
        /* Pagination Styling */
        .pagination {
            margin: 0;
            gap: 4px;
        }
        .pagination .page-link {
            border: 1px solid #e3e6f0;
            color: #5a5c69;
            background-color: #fff;
            padding: 0.5rem 0.75rem;
            border-radius: 0.35rem;
            transition: all 0.15s ease-in-out;
            font-weight: 500;
            min-width: 40px;
            text-align: center;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        .pagination .page-link:hover {
            background-color: #eaecf4;
            border-color: #d1d3e2;
            color: #2e59d9;
            transform: translateY(-1px);
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.25);
        }
        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: #667eea;
            color: #fff;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(102, 126, 234, 0.4);
        }
        .pagination .page-item.disabled .page-link {
            color: #b7b9cc;
            background-color: #f8f9fc;
            border-color: #e3e6f0;
            cursor: not-allowed;
            box-shadow: none;
        }
        .pagination .page-link:focus {
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            outline: none;
        }
        
        /* Arrow icons styling */
        .pagination .page-link i {
            font-size: 0.875rem;
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
            
            /* Mobile pagination */
            .pagination {
                gap: 2px;
            }
            .pagination .page-link {
                padding: 0.375rem 0.5rem;
                min-width: 35px;
                font-size: 0.875rem;
            }
            
            /* Hide page numbers on very small screens, show only arrows */
            @media (max-width: 400px) {
                .pagination .page-item:not(.page-item:first-child):not(.page-item:last-child) {
                    display: none;
                }
            }
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="header-bar">
            <div class="header-title">Lead Clients</div>
            <div class="header-actions">
                <a href="{{ route('lead-clients.create') }}" class="btn btn-primary">Create New Lead</a>
                <a href="{{ route('call-history.index') }}" class="btn btn-info">Call History</a>
                <form action="{{ url('/logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">Logout</button>
                </form>
            </div>
        </div>
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <!-- Navigation Tabs -->
        <ul class="nav nav-tabs mb-4" id="mainTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="leads-tab" data-bs-toggle="tab" data-bs-target="#leads" type="button" role="tab">
                    Lead Clients
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="calls-tab" data-bs-toggle="tab" data-bs-target="#calls" type="button" role="tab">
                    Recent Calls
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="mainTabsContent">
            <!-- Lead Clients Tab -->
            <div class="tab-pane fade show active" id="leads" role="tabpanel">
                
                <!-- Search and Filter Form -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="{{ route('lead-clients.index') }}" class="row g-3">
                            <div class="col-md-4">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ $search ?? '' }}" placeholder="Search by name, email, or phone">
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">All Statuses</option>
                                    <option value="new_lead" {{ ($status ?? '') == 'new_lead' ? 'selected' : '' }}>New Lead</option>
                                    <option value="spam" {{ ($status ?? '') == 'spam' ? 'selected' : '' }}>Spam</option>
                                    <option value="junk" {{ ($status ?? '') == 'junk' ? 'selected' : '' }}>Junk</option>
                                    <option value="clear" {{ ($status ?? '') == 'clear' ? 'selected' : '' }}>Clear</option>
                                    <option value="unmarked" {{ ($status ?? '') == 'unmarked' ? 'selected' : '' }}>Unmarked</option>
                                    <option value="uncontacted" {{ ($status ?? '') == 'uncontacted' ? 'selected' : '' }}>Uncontacted</option>
                                    <option value="contacted" {{ ($status ?? '') == 'contacted' ? 'selected' : '' }}>Contacted</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="lead_type" class="form-label">Lead Type</label>
                                <select class="form-select" id="lead_type" name="lead_type">
                                    <option value="">All Types</option>
                                    <option value="manual" {{ ($leadType ?? '') == 'manual' ? 'selected' : '' }}>Manual</option>
                                    <option value="webhook" {{ ($leadType ?? '') == 'webhook' ? 'selected' : '' }}>Webhook</option>
                                    <option value="ppc" {{ ($leadType ?? '') == 'ppc' ? 'selected' : '' }}>PPC</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <div class="d-grid gap-2 w-100">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    @if($search || $status || $leadType)
                                        <a href="{{ route('lead-clients.index') }}" class="btn btn-outline-secondary">Clear</a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile Number</th>
                            <th>Status</th>
                            <th>Lead Type</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($leadClients as $client)
                            <tr>
                                <td>{{ $client->id }}</td>
                                <td>{{ $client->name }}</td>
                                <td>{{ $client->email }}</td>
                                <td>{{ $client->mobile_number }}</td>
                                <td>{{ $client->status }}</td>
                                <td>{{ $client->lead_type }}</td>
                                <td>{{ $client->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No lead clients found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                
                <!-- Custom Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Showing {{ $leadClients->firstItem() ?? 0 }} to {{ $leadClients->lastItem() ?? 0 }} of {{ $leadClients->total() }} results
                    </div>
                    <nav aria-label="Lead clients pagination">
                        <ul class="pagination pagination-sm mb-0">
                            {{-- Previous Page Link --}}
                            @if ($leadClients->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link">
                                        <i class="fas fa-chevron-left"></i>
                                    </span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $leadClients->previousPageUrl() }}" rel="prev">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                            @endif

                            {{-- Pagination Elements --}}
                            @foreach ($leadClients->getUrlRange(1, $leadClients->lastPage()) as $page => $url)
                                @if ($page == $leadClients->currentPage())
                                    <li class="page-item active">
                                        <span class="page-link">{{ $page }}</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                    </li>
                                @endif
                            @endforeach

                            {{-- Next Page Link --}}
                            @if ($leadClients->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $leadClients->nextPageUrl() }}" rel="next">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            @else
                                <li class="page-item disabled">
                                    <span class="page-link">
                                        <i class="fas fa-chevron-right"></i>
                                    </span>
                                </li>
                            @endif
                        </ul>
                    </nav>
                </div>
            </div>

            <!-- Recent Calls Tab -->
            <div class="tab-pane fade" id="calls" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Phone Number</th>
                                <th>Email</th>
                                <th>Lead Client</th>
                                <th>Status</th>
                                <th>Call Time</th>
                                <th>Call SID</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentCalls ?? [] as $call)
                                <tr>
                                    <td>{{ $call->phone_number }}</td>
                                    <td>{{ $call->email ?? 'N/A' }}</td>
                                    <td>
                                        @if($call->leadClient)
                                            {{ $call->leadClient->name }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        <span class="call-status {{ $call->call_status }}">
                                            {{ ucfirst($call->call_status) }}
                                        </span>
                                    </td>
                                    <td>{{ $call->call_timestamp->format('Y-m-d H:i:s') }}</td>
                                    <td>{{ $call->call_sid ?? 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No call history found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Call History Modal -->
    <div class="modal fade" id="callHistoryModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Call History for Lead Client</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="callHistoryContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
    <br><br>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showCallHistory(leadClientId) {
            // This would typically make an AJAX call to get call history for a specific lead
            // For now, we'll just show a placeholder
            document.getElementById('callHistoryContent').innerHTML = 
                '<p>Loading call history for lead client ID: ' + leadClientId + '</p>';
            
            // You can implement AJAX here to fetch call history
            // fetch('/api/lead-client/' + leadClientId + '/calls')
            //     .then(response => response.json())
            //     .then(data => {
            //         // Display the call history
            //     });
            
            new bootstrap.Modal(document.getElementById('callHistoryModal')).show();
        }
    </script>
</body>
</html> 