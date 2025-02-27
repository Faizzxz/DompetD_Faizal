<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kuni's</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }
        .sidebar {
            width: 250px;
            background-color: #343a40;
            padding: 20px;
            color: white;
            min-height: 100vh;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .content {
            flex: 1;
            padding: 20px;
        }
        .card {
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h4>Admin Dashboard</h4>
        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
        <a href="#" data-bs-toggle="modal" data-bs-target="#addRoleModal">Add User</a>
        <form action="{{ url('admin/logout') }}" method="POST" class="mt-3">
            @csrf
            <button type="submit" class="btn btn-danger w-100">Logout</button>
        </form>
    </div>

    <div class="content">
        <h2>Welcome, {{ $admin->name }}</h2>
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white p-3">
                    <h5>Admin Users</h5>
                    <p>{{ count($adminUsers) }} Users</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white p-3">
                    <h5>Siswa Users</h5>
                    <p>{{ count($siswaUsers) }} Users</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-white p-3">
                    <h5>Bank Users</h5>
                    <p>{{ count($bankUsers) }} Users</p>
                </div>
            </div>
        </div>

        <h3 class="mt-5">User List</h3>
        <table class="table table-striped table-bordered mt-3">
            <thead class="table-dark">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach(array_merge($adminUsers->toArray(), $siswaUsers->toArray(), $bankUsers->toArray()) as $user)
                    <tr>
                        <td>{{ $user['name'] }}</td>
                        <td>{{ $user['email'] }}</td>
                        <td>{{ $user['role'] }}</td>
                        <td>
                            <a href="{{ route('admin.edit', $user['id']) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('admin.delete', $user['id']) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- MODAL ADD USER -->
    <div class="modal fade" id="addRoleModal" tabindex="-1" aria-labelledby="addRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addRoleModalLabel">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.addRole') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="admin">Admin</option>
                                <option value="siswa">Siswa</option>
                                <option value="bank">Bank</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Add User</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
