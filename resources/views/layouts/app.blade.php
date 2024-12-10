<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Expense Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 w-64 bg-gray-800">
        <div class="flex items-center justify-center h-16 bg-gray-900">
            <h1 class="text-xl font-bold text-white">Expense Tracker</h1>
        </div>
        <nav class="mt-6">
            <div class="px-4 space-y-3">
                <a href="/home" class="flex items-center px-4 py-2 text-gray-100 bg-gray-700 rounded-lg">
                    <span class="ml-3">Dashboard</span>
                </a>
                <a href="/admin/users" class="flex items-center px-4 py-2 text-gray-400 hover:text-gray-100 hover:bg-gray-700 rounded-lg">
                    <span class="ml-3">Users</span>
                </a>
                <a href="/admin/expenses" class="flex items-center px-4 py-2 text-gray-400 hover:text-gray-100 hover:bg-gray-700 rounded-lg">
                    <span class="ml-3">Expenses</span>
                </a>
                <a href="/admin/budgets" class="flex items-center px-4 py-2 text-gray-400 hover:text-gray-100 hover:bg-gray-700 rounded-lg">
                    <span class="ml-3">Budgets</span>
                </a>
            </div>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="ml-64">
        <!-- Top Navigation Bar -->
        <nav class="bg-white shadow-lg">
            <div class="px-4">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <h2 class="text-xl font-semibold text-gray-800">Admin Dashboard</h2>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-600">Admin</span>
                        <a href="{{ route('admin.logout') }}" 
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                           class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                            Logout
                        </a>
                        <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        @yield('content')

        {{-- <div class="p-8 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Total Expenses</h3>
                    <p class="text-3xl font-bold text-blue-600">{{ $totalExpenses ?? 0 }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Total Budget</h3>
                    <p class="text-3xl font-bold text-green-600">{{ $totalBudget ?? 0 }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Remaining Budget</h3>
                    <p class="text-3xl font-bold text-purple-600">{{ $remainingBudget ?? 0 }}</p>
                </div>
            </div>
            <!-- Monthly Expenses Chart -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Monthly Expenses Trend</h2>
                    <div class="w-full h-96">
                        <canvas id="monthlyExpensesLine"></canvas>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Expenses by Category</h2>
                    <div class="w-full h-96">
                        <canvas id="monthlyExpensesPie"></canvas>
                    </div>
                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const expenses = @json($users->pluck('expenses')->flatten());
                    
                    // Prepare data for pie chart
                    const expensesByCategory = expenses.reduce((acc, expense) => {
                        acc[expense.category] = (acc[expense.category] || 0) + parseFloat(expense.amount);
                        return acc;
                    }, {});

                    const categories = Object.keys(expensesByCategory);
                    const categoryAmounts = Object.values(expensesByCategory);
                    const colors = categories.map(() => 
                        '#' + Math.floor(Math.random()*16777215).toString(16).padStart(6, '0')
                    );

                    // Prepare data for line chart
                    const expensesByMonth = expenses.reduce((acc, expense) => {
                        const date = new Date(expense.expense_date);
                        const monthYear = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
                        acc[monthYear] = (acc[monthYear] || 0) + parseFloat(expense.amount);
                        return acc;
                    }, {});

                    const months = Object.keys(expensesByMonth).sort();
                    const monthlyAmounts = months.map(month => expensesByMonth[month]);

                    // Create pie chart
                    const pieCtx = document.getElementById('monthlyExpensesPie').getContext('2d');
                    new Chart(pieCtx, {
                        type: 'pie',
                        data: {
                            labels: categories,
                            datasets: [{
                                data: categoryAmounts,
                                backgroundColor: colors,
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'right'
                                }
                            }
                        }
                    });

                    // Create line chart
                    const lineCtx = document.getElementById('monthlyExpensesLine').getContext('2d');
                    new Chart(lineCtx, {
                        type: 'line',
                        data: {
                            labels: months,
                            datasets: [{
                                label: 'Monthly Expenses',
                                data: monthlyAmounts,
                                borderColor: '#4F46E5',
                                tension: 0.1,
                                fill: false
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                });
            </script> --}}

            

          
    </div>
</body>
</html>