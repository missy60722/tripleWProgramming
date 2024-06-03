document.addEventListener('DOMContentLoaded', function () {
    const accountForm = document.forms["accountForm"];
    accountForm.addEventListener('submit', function (event) {
        event.preventDefault();

        if (validateForm()) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'add_transaction.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onload = function () {
                if (xhr.status === 200) {
                    fetchTransactions();
                    accountForm.reset(); 
                } else {
                    console.error('提交表單失敗。狀態:', xhr.status);
                }
            };
            xhr.onerror = function () {
                console.error('提交表單時出錯。');
            };
            xhr.send(new URLSearchParams(new FormData(accountForm)));
        }
    });
});

function fetchTransactions() {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'get_transaction.php', true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            const transactions = JSON.parse(xhr.responseText);
            if (document.body.id === "account") {
                createAccountCharts(transactions);
            } else {
                updateNumberDisplay(transactions);
                createCharts(transactions);
            }
        } else {
            console.error('Failed to fetch transactions. Status:', xhr.status);
        }
    };
    xhr.onerror = function () {
        console.error('Error occurred while fetching transactions.');
    };
    xhr.send();
}

/* 記帳 */

function createAccountCharts(transactions) {
    const categories = transactions.map(transaction => transaction.category);
    const uniqueCategories = Array.from(new Set(categories));
    uniqueCategories.sort();

    createDonutChart(transactions, uniqueCategories);
}

function createDonutChart(transactions, categories) {
    d3.select("#donutChart").selectAll("*").remove();
    
    const categoryAmounts = {};

    transactions.forEach(transaction => {
        const category = transaction.category;
        categoryAmounts[category] = (categoryAmounts[category] || 0) + parseFloat(transaction.amount);
    });

    const data = categories.map(category => ({
        category,
        amount: categoryAmounts[category] || 0
    }));
    data.sort((a, b) => b.amount - a.amount);

    const margin = { top: 20, right: 30, bottom: 30, left: 40 };
    const width = 400 - margin.left - margin.right;
    const height = 400 - margin.top - margin.bottom;
    const radius = Math.min(width, height) / 2;
    
    const customColors = [
        "#f8e5d7", "#4a698a", "#ffd188", "#708ba2", "#88959b", "#5e7f96",
        "#5a7ea4", "#59798a", "#b0c6d8", "#606d77", "#62869c", "#627e99",
        "#fff2c1", "#859093", "#b9a4a3", "#4d5860", "#b1bcc2", "#f6bf9f",
        "#525b5f", "#25415d", "#7c9bbb"
    ];

    const color = d3.scaleOrdinal()
        .domain(data.map(d => d.category))
        .range(customColors);
    
    const arc = d3.arc()
        .outerRadius(radius - 10)
        .innerRadius(radius - 70);
    
    const pie = d3.pie()
        .sort(null)
        .value(d => d.amount);
    
    const svg = d3.select("#donutChart")
        .append("svg")
        .attr("width", width)
        .attr("height", height)
        .append("g")
        .attr("transform", `translate(${width / 2},${height / 2})`);
    
    const tooltip = d3.select("#chartTooltip");
    
    const g = svg.selectAll(".arc")
        .data(pie(data))
        .enter().append("g")
        .attr("class", "arc")
        .on("mouseover", function (event, d) {
            const percent = ((d.data.amount / d3.sum(data, d => d.amount)) * 100).toFixed(2);
            d3.select(this)
                .transition()
                .duration(200)
                .attr("transform", `scale(1.1)`);
            tooltip.style("opacity", 1)
                .html(`${d.data.category}：${percent}%<br>金額：${d.data.amount}元`)
                .style('left', (event.pageX + 10) + 'px')
                .style('top', (event.pageY - 20) + 'px');
        })
        .on("mousemove", function (event) {
            tooltip.style('left', (event.pageX + 10) + 'px')
                .style('top', (event.pageY - 20) + 'px');
        })
        .on("mouseout", function () {
            d3.select(this)
                .transition()
                .duration(200)
                .attr("transform", `scale(1)`);
            tooltip.style("opacity", 0);
        });
    
    g.append("path")
        .attr("d", arc)
        .style("fill", d => color(d.data.category))
    
    const legend = svg.selectAll("#legend")
        .data(data)
        .enter().append("g")
        .attr("class", "legend")
        .attr("transform", function(d, i) {
            return "translate(" + (width - 150) + "," + (i * 20 + 50) + ")";
        });

    legend.append("rect")
        .attr("x", 0)
        .attr("width", 18)
        .attr("height", 18)
        .style("fill", function(d) { return color(d.category); });

    legend.append("text")
        .attr("x", 25)
        .attr("y", 9)
        .attr("dy", ".35em")
        .text(function(d) { return d.category; });

    updateNumberDisplay(transactions);

}

/* 統計數據 */

function createCharts(transactions) {
    const categories = transactions.map(transaction => transaction.category);
    const uniqueCategories = Array.from(new Set(categories));

    uniqueCategories.sort();

    createPieChart(transactions, uniqueCategories);
    createLineChart(transactions);
    createBarChart(transactions);
}

function createPieChart(transactions, categories) {
    const categoryAmounts = {};

    transactions.forEach(transaction => {
        const category = transaction.category;
        categoryAmounts[category] = (categoryAmounts[category] || 0) + parseFloat(transaction.amount);
    });

    const data = categories.map(category => ({
        category,
        amount: categoryAmounts[category] || 0
    }));
    data.sort((a, b) => b.amount - a.amount);

    const margin = { top: 20, right: 20, bottom: 20, left: 20 };
    const width = 600 - margin.left - margin.right;
    const height = 600 - margin.top - margin.bottom;
    const radius = Math.min(width, height) / 2;
    
    const customColors = [
        "#f8e5d7", "#4a698a", "#ffd188", "#708ba2", "#88959b", "#5e7f96",
        "#5a7ea4", "#59798a", "#b0c6d8", "#606d77", "#62869c", "#627e99",
        "#fff2c1", "#859093", "#b9a4a3", "#4d5860", "#b1bcc2", "#f6bf9f",
        "#525b5f", "#25415d", "#7c9bbb"
    ];

    const color = d3.scaleOrdinal()
        .domain(data.map(d => d.category))
        .range(customColors);

    const pie = d3.pie()
        .sort((a, b) => b.amount - a.amount)
        .value(d => d.amount);

    const arc = d3.arc()
        .outerRadius(radius - 10)
        .innerRadius(0);

    const svg = d3.select("#pieChart")
        .attr("width", width)
        .attr("height", height)
        .append("g")
        .attr("transform", `translate(${width / 2},${height / 2})`);

    const tooltip = d3.select("#chartTooltip");

    const arcs = svg.selectAll(".arc")
        .data(pie(data))
        .enter()
        .append("g")
        .attr("class", "arc-group")
        .on("mouseover", function (event, d) {
        const percent = ((d.data.amount / d3.sum(data, d => d.amount)) * 100).toFixed(2);
            d3.select(this)
                .transition()
                .duration(200)
                .attr("transform", `scale(1.1)`);
            tooltip.style("opacity", 1)
                .html(`${d.data.category}：${percent}%<br>金額：${d.data.amount}元`)
                .style('left', (event.pageX + 10) + 'px')
                .style('top', (event.pageY - 20) + 'px');
        })
        .on("mousemove", function (event) {
            tooltip.style('left', (event.pageX + 10) + 'px')
                .style('top', (event.pageY - 20) + 'px');
        })
        .on("mouseout", function () {
            d3.select(this)
                .transition()
                .duration(200)
                .attr("transform", `scale(1)`);
            tooltip.style("opacity", 0);
        });        

    arcs.append("path")
        .attr("d", arc)
        .style("fill", d => color(d.data.category))
        .attr("stroke", "white");

    arcs.filter(d => (d.data.amount / d3.sum(data, d => d.amount)) * 100 >= 10)
        .append("text")
        .attr("transform", d => {
            const [x, y] = arc.centroid(d);
            const labelX = x * 1.1; 
            const labelY = y * 1; 
            return `translate(${labelX}, ${labelY})`;
        })
        .attr("dy", "0.35em")
        .style("text-anchor", "middle") 
        .style("font-size", "24px")
        .text(d => d.data.category);
}

function createLineChart(transactions) {
    const parseTime = d3.timeParse("%Y-%m-%d");
    const formatTime = d3.timeFormat("%Y-%m-%d");

    transactions.forEach(transaction => {
        transaction.created_at = parseTime(transaction.created_at);
        if (transaction.type === "支出") {
            transaction.newAmount = -parseFloat(transaction.amount);
        } else {
            transaction.newAmount = parseFloat(transaction.amount);
        }
    });

    const margin = { top: 20, right: 0, bottom: 90, left: 50 };
    const width = 600 - margin.left - margin.right;
    const height = 600 - margin.top - margin.bottom;

    const x = d3.scaleTime()
        .range([0, width]);

    const lowestAmount = d3.min(transactions, d => d.newAmount) * 1.2;
    const highestAmount = d3.max(transactions, d => d.newAmount);

    const y = d3.scaleLinear()
        .domain([lowestAmount, highestAmount])
        .range([height, 0]);

    const line = d3.line()
        .x(d => x(d.created_at))
        .y(d => y(d.newAmount))
        .curve(d3.curveMonotoneX);

    const svg = d3.select("#lineChart")
        .attr("width", width + margin.left + margin.right)
        .attr("height", height + margin.top + margin.bottom)
        .append("g")
        .attr("transform", `translate(${margin.left},${margin.top})`);

    const dataDates = transactions.map(d => d.created_at);

    x.domain(d3.extent(dataDates));

    svg.append("path")
        .datum(transactions)
        .style("fill", "none")
        .attr("stroke", "#708ba2")
        .attr("stroke-width", 2)
        .attr("d", line);

    const tooltip = d3.select("#chartTooltip");

    svg.selectAll(".dot")
        .data(transactions)
        .enter().append("circle")
        .attr("class", "dot")
        .attr("cx", function (d) { return x(d.created_at); })
        .attr("cy", function (d) { return y(d.newAmount); })
        .attr("r", 4)
        .style("fill", function(d) {
            return d.newAmount > 0 ? "#b0c6d8" : "#ffd188";
        })
        .on("mouseover", function(event, d) {
            d3.select(this).attr("r", 5)
                .style("fill", "#4d5860");
            tooltip.style("opacity", 1)
                .html(`日期：${formatTime(d.created_at)}<br>類型：${d.type}<br>項目：${d.name}<br>金額：${d.amount}元`)
                .style('left', (event.pageX + 10) + 'px')
                .style('top', (event.pageY - 20) + 'px');
        })
        .on("mouseout", function () {
            d3.select(this).attr("r", 4)
                .style("fill", function(d) {
                    return d.newAmount > 0 ? "#b0c6d8" : "#ffd188";
                });
            tooltip.style("opacity", 0);
        });

        
    const minDate = d3.min(transactions, d => d.created_at);
    const maxDate = d3.max(transactions, d => d.created_at);
    
    const tickValues = [];
    let currentDate = new Date(minDate);
    
    while (currentDate <= maxDate) {
        tickValues.push(currentDate);
        const nextDate = new Date(currentDate);
        nextDate.setDate(currentDate.getDate() + 10);
        if (nextDate <= maxDate) {
            currentDate = nextDate;
        } else {
            break;
        }
    }
        
    svg.append("g")
        .attr("transform", `translate(0,${height})`)
        .call(d3.axisBottom(x)
            .tickValues(tickValues)
            .tickFormat(formatTime))
        .selectAll("text")
        .attr("transform", "rotate(-45)")
        .style("text-anchor", "end");

    svg.append("g")
        .call(d3.axisLeft(y));

    svg.append("line")
        .attr("x1", 0)
        .attr("y1", y(0))
        .attr("x2", width)
        .attr("y2", y(0))
        .attr("stroke", "black")
        .attr("stroke-dasharray", "2");

    svg.append("text")
        .attr("transform", "rotate(-90)")
        .attr("y", 0 - margin.left - 40)
        .attr("x", 0 - (height / 2))
        .attr("dy", "1em")
        .style("text-anchor", "middle")
        .text("金額");

    svg.append("text")
        .attr("transform", `translate(${width / 2 + margin.right}, ${height + margin.top + 70})`)
        .style("text-anchor", "middle")
        .text("日期");

    const legendData = ["收入", "支出"];
    const legendColors = ["#b0c6d8", "#ffd188"];

    const legend = svg.selectAll("#legend")
        .data(legendData)
        .enter().append("g")
        .attr("class", "legend")
        .attr("transform", function (d, i) {
            return "translate(0," + i * 20 + ")";
        });

    legend.append("rect")
        .attr("x", width - 50)
        .attr("y", -30)
        .attr("width", 18)
        .attr("height", 18)
        .style("fill", function (d, i) {
            return legendColors[i];
        });

    legend.append("text")
        .attr("x", width + 16)
        .attr("y", -21)
        .attr("dy", ".35em")
        .attr("text-anchor", "end")
        .text(function (d) {
            return d;
        });
}

function createBarChart(transactions) {
    const monthlyData = {};
    transactions.forEach(transaction => {
        const monthYear = transaction.created_at.getFullYear() + '-' + (transaction.created_at.getMonth() + 1);
        if (!monthlyData[monthYear]) {
            monthlyData[monthYear] = { income: 0, expense: 0 };
        }
        if (transaction.type === '收入') {
            monthlyData[monthYear].income += parseFloat(transaction.amount);
        } else {
            monthlyData[monthYear].expense += parseFloat(transaction.amount);
        }
    });

    const allMonths = generateAllMonths(transactions);
    allMonths.reverse();

    const data = allMonths.map(monthYear => {
        const income = monthlyData[monthYear] ? monthlyData[monthYear].income : 0;
        const expense = monthlyData[monthYear] ? monthlyData[monthYear].expense : 0;
        const netIncome = income - expense;
        return { monthYear, income, expense, netIncome };
    });

    const margin = { top: 20, right: 40, bottom: 90, left: 30 };
    const width = 600 - margin.left - margin.right;
    const height = 600 - margin.top - margin.bottom;

    const x = d3.scaleBand()
        .domain(data.map(d => d.monthYear))
        .range([0, width])
        .align(0.5);

    const y = d3.scaleLinear()
        .domain([
            d3.min(data, d => Math.min(d.income, d.expense)),
            d3.max(data, d => Math.max(d.income, d.expense))
        ])
        .nice()
        .range([height, 0]);

    const yLine = d3.scaleLinear()
        .domain([d3.min(data, d => d.netIncome), d3.max(data, d => d.netIncome)])
        .nice()
        .range([height, 0]);

    const svg = d3.select("#barChart")
        .attr("width", width + margin.left + margin.right)
        .attr("height", height + margin.top + margin.bottom)
        .append("g")
        .attr("transform", `translate(${margin.left},${margin.top})`);

    const barWidth = 30;

    const tooltip = d3.select("#chartTooltip");

    svg.selectAll(".income-bar")
    .data(data)
    .enter().append("rect")
    .attr("class", "income-bar")
    .attr("x", d => x(d.monthYear) + barWidth * 1.1)
    .attr("y", d => y(d.income))
    .attr("width", barWidth)
    .attr("height", d => height - y(d.income))
    .style("fill", "#b0c6d8")
    .on("mouseover", function (event, d) {
        const percent = ((d.income / (d.income + d.expense)) * 100).toFixed(2);
        tooltip.style("opacity", 1)
            .html(`${d.monthYear}<br>收入：${d.income}元<br>收入佔比：${percent}%`)
            .style('left', (event.pageX + 10) + 'px')
            .style('top', (event.pageY - 20) + 'px');
    })
    .on("mousemove", function (event) {
        tooltip.style('left', (event.pageX + 10) + 'px')
            .style('top', (event.pageY - 20) + 'px');
    })
    .on("mouseout", function () {
        tooltip.style("opacity", 0);
    });

svg.selectAll(".expense-bar")
    .data(data)
    .enter().append("rect")
    .attr("class", "expense-bar")
    .attr("x", d => x(d.monthYear) + barWidth * 2.3)
    .attr("y", d => y(d.expense))
    .attr("width", barWidth)
    .attr("height", d => height - y(d.expense))
    .style("fill", "#ffd188")
    .on("mouseover", function (event, d) {
        const percent = ((d.expense / (d.income + d.expense)) * 100).toFixed(2);
        tooltip.style("opacity", 1)
            .html(`${d.monthYear}<br>支出：${d.expense}元<br>支出佔比：${percent}%`)
            .style('left', (event.pageX + 10) + 'px')
            .style('top', (event.pageY - 20) + 'px');
    })
    .on("mousemove", function (event) {
        tooltip.style('left', (event.pageX + 10) + 'px')
            .style('top', (event.pageY - 20) + 'px');
    })
    .on("mouseout", function () {
        tooltip.style("opacity", 0);
    });

    const xAxis = d3.axisBottom(x).tickSizeOuter(0);

    svg.append("g")
        .attr("class", "x-axis")
        .attr("transform", `translate(0, ${height})`)
        .call(xAxis)
        .selectAll("text")
        .attr("transform", "rotate(-45)")
        .style("text-anchor", "end")
        .attr("dx", "-0.5em")
        .attr("dy", "0.5em");

    svg.append("g")
        .attr("class", "y-axis")
        .call(d3.axisLeft(y));

    const line = d3.line()
        .x(d => x(d.monthYear) + barWidth * 2.2)
        .y(d => yLine(d.netIncome))
        .curve(d3.curveMonotoneX);

    svg.append("path")
        .datum(data)
        .style("fill", "none")
        .style("stroke", "#859093")
        .attr("stroke-width", 2)
        .attr("d", line);

    svg.selectAll(".dot")
        .data(data)
        .enter().append("circle")
        .attr("class", "dot")
        .attr("cx", d => x(d.monthYear) + barWidth * 2.2)
        .attr("cy", d => yLine(d.netIncome))
        .attr("r", 4)
        .style("fill", "#859093")
        .on("mouseover", function (event, d) {
            d3.select(this).attr("r", 5)
                .style("fill", "#f8e5d7");
            tooltip.style("opacity", 1)
                .html(`${d.monthYear}<br>淨收入：${d.netIncome}元`)
                .style('left', (event.pageX + 10) + 'px')
                .style('top', (event.pageY - 20) + 'px');
        })
        .on("mousemove", function (event) {
            tooltip.style('left', (event.pageX + 10) + 'px')
                .style('top', (event.pageY - 20) + 'px');
        })
        .on("mouseout", function () {
            d3.select(this).attr("r", 4)
                .style("fill", "#859093");
            tooltip.style("opacity", 0);
        });
    
    svg.append("g")
        .attr("class", "y-axis")
        .attr("transform", `translate(${width}, 0)`)
        .call(d3.axisRight(yLine));

    svg.append("text")
        .attr("transform", `translate(${width + margin.right + 35}, ${height / 2 - 20}) rotate(90)`)
        .attr("dy", "1em")
        .style("text-anchor", "middle")
        .text("淨收入");

    svg.append("text")
        .attr("transform", `translate(${width / 2 + margin.right}, ${height + margin.top + 70})`)
        .style("text-anchor", "middle")
        .text("月份");

    svg.append("text")
        .attr("transform", "rotate(-90)")
        .attr("y", 0 - margin.left - 40)
        .attr("x", 0 - (height / 2))
        .attr("dy", "1em")
        .style("text-anchor", "middle")
        .text("金額");

    const legendData = ["收入", "支出"];
    const legendColors = ["#b0c6d8", "#ffd188"];

    const legend = svg.selectAll("#legend")
        .data(legendData)
        .enter().append("g")
        .attr("class", "legend")
        .attr("transform", function (d, i) {
            return "translate(0," + i * 20 + ")";
        });

    legend.append("rect")
        .attr("x", width - 10)
        .attr("y", -70)
        .attr("width", 18)
        .attr("height", 18)
        .style("fill", function (d, i) {
            return legendColors[i];
        });

    legend.append("text")
        .attr("x", width + 56)
        .attr("y", -61)
        .attr("dy", ".35em")
        .attr("text-anchor", "end")
        .text(function (d) {
            return d;
        });
}

function generateAllMonths(transactions) {
    const allMonths = [];
    transactions.forEach(transaction => {
        const monthYear = transaction.created_at.getFullYear() + '-' + (transaction.created_at.getMonth() + 1);
        if (!allMonths.includes(monthYear)) {
            allMonths.push(monthYear);
        }
    });
    return allMonths.sort((a, b) => new Date(a) - new Date(b));
}

function updateNumberDisplay(transactions) {
    let totalIncome = 0;
    let totalExpense = 0;

    transactions.forEach(transaction => {
        if (transaction.type === '收入') {
            totalIncome += parseFloat(transaction.amount);
        } else {
            totalExpense += parseFloat(transaction.amount);
        }
    });

    const balance = totalIncome - totalExpense;

    document.getElementById("totalIncome").textContent = totalIncome;
    document.getElementById("totalExpense").textContent = totalExpense;
    document.getElementById("balance").textContent = balance;
}

fetchTransactions();