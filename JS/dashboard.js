fetch("./../fetch_chart_data.php")
    .then(res => res.json())
    .then(data => {
        const ctx = document.getElementById("monthlyChart").getContext("2d");

        new Chart(ctx, {
            type: "bar",
            data: {
                labels: data.labels,
                datasets: [
                    {
                        label: "Profit",
                        data: data.profits,
                        yAxisID: "y",
                    },
                    {
                        label: "Expense",
                        data: data.expenses,
                        yAxisID: "y",
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
