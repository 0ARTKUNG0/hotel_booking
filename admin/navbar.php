<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">Admin Panel</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="rooms.php">Manage Rooms</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="bookings.php">Manage Bookings</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="users.php">Manage Users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<style>
.navbar {
    background-color: #1a1a1a;
    padding: 1rem 2rem;
}

.navbar-brand {
    color: white !important;
    font-weight: bold;
    margin-right: 2rem;
}

.nav-link {
    color: #999 !important;
    padding: 0.5rem 1rem !important;
    transition: color 0.3s;
}

.nav-link:hover {
    color: white !important;
}

.nav-item {
    margin-right: 1rem;
}

.navbar-nav {
    align-items: center;
}
</style>