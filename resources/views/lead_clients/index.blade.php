<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lead Clients</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
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
                {!! $leadClients->links() !!}
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