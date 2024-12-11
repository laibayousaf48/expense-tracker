{{-- @extends('layouts.app')

@section('content')
        <!-- Page Content -->
        <div class="p-8 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Total Users</h3>
                    <p class="text-3xl font-bold text-blue-600">{{ $users }}</p>
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
                    // Get categories data passed from controller
                    const categories = @json($categories ?? []);
                    const expenses = @json($expenses ?? []);
                    console.log(expenses);
                    console.log(categories);
                    // Prepare data for pie chart
                    const categoryNames = Object.values(categories).map(cat => cat.name);
                    const expenseAmounts = Object.values(categories).map(cat => {
                        const categoryExpenses = expenses.filter(exp => exp.category.name === cat.name);
                        return categoryExpenses.reduce((sum, exp) => sum + parseFloat(exp.amount), 0);
                    });

                    const colors = categoryNames.map(() => 
                        '#' + Math.floor(Math.random()*16777215).toString(16).padStart(6, '0')
                    );

                    // Prepare data for line chart
                    const expensesByMonth = {};
                    expenses.forEach(expense => {
                        if (expense && expense.expense_date && expense.amount) {
                            const date = new Date(expense.expense_date);
                            const monthYear = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
                            if (!expensesByMonth[monthYear]) {
                                expensesByMonth[monthYear] = {};
                                categoryNames.forEach(cat => expensesByMonth[monthYear][cat] = 0);
                            }
                            expensesByMonth[monthYear][expense.category.name] += parseFloat(expense.amount);
                        }
                    });

                    const months = Object.keys(expensesByMonth).sort();

                    // Create pie chart
                    const pieCtx = document.getElementById('monthlyExpensesPie').getContext('2d');
                    new Chart(pieCtx, {
                        type: 'pie',
                        data: {
                            labels: categoryNames,
                            datasets: [{
                                data: expenseAmounts,
                                backgroundColor: colors,
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'right',
                                    labels: {
                                        generateLabels: function(chart) {
                                            const data = chart.data;
                                            return data.labels.map((label, i) => ({
                                                text: `${label}: $${expenseAmounts[i].toFixed(2)}`,
                                                fillStyle: colors[i],
                                                index: i
                                            }));
                                        }
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const label = context.label || '';
                                            const value = context.parsed || 0;
                                            return `${label}: $${value.toFixed(2)}`;
                                        }
                                    }
                                }
                            }
                        }
                    });

                    // Create line chart with category breakdown
                    const lineCtx = document.getElementById('monthlyExpensesLine').getContext('2d');
                    new Chart(lineCtx, {
                        type: 'line',
                        data: {
                            labels: months,
                            datasets: categoryNames.map((category, index) => ({
                                label: category,
                                data: months.map(month => expensesByMonth[month][category]),
                                borderColor: colors[index],
                                tension: 0.1,
                                fill: false
                            }))
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return '$' + value.toFixed(2);
                                        }
                                    }
                                }
                            },
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return `${context.dataset.label}: $${context.parsed.y.toFixed(2)}`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                });
            </script>
        </div>
@endsection --}}

<h1>Dashboard</h1>