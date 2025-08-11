var selector_bar = $("#total_collection");
var selector_pie = $("#collection_status");
var selector_pie_krd = $("#collection_krd");
var selector_pie_kc = $("#collection_kc");
var selector_pie_kra = $("#collection_kra");
var selector_pie_total = $("#collection_total");
var selector_line = $("#collection_last_day");
var selector_stacked_bar = $("#file_type");

var optionBar = {
responsive: true,
maintainAspectRatio: false,
responsiveAnimationDuration: 500,
elements: {
rectangle: {
borderWidth: 2,
borderColor: "rgb(0, 255, 0)",
borderSkipped: "bottom",
},
},
legend: {
position: "top",
},
scales: {
xAxes: [
{
display: true,
gridLines: {
color: "#f3f3f3",
drawTicks: false,
},
scaleLabel: {
display: true,
},
},
],
yAxes: [
{
display: true,
gridLines: {
color: "#f3f3f3",
drawTicks: false,
},
scaleLabel: {
display: true,
},
},
],
},
};

var optionPie = {
responsive: true,
maintainAspectRatio: false,
responsiveAnimationDuration: 500,
tooltips: {
enabled: false,
},
plugins: {
datalabels: {
formatter: (value, ctx) => {
let sum = 0;
let dataArr = ctx.chart.data.datasets[0].data;
dataArr.map((data) => {
sum += data;
});
let percentage = ((value * 100) / sum).toFixed(2);
return percentage > 0 ? percentage + "%" : "";
},
display: true,
anchor: "end",
align: "top",
color: "black",
font: {
weight: "bold",
size: 14,
},
},
},
};

var optionLine = {
responsive: true,
maintainAspectRatio: false,
responsiveAnimationDuration: 500,
};

var optionStackedBar = {
responsive: true,
maintainAspectRatio: false,
responsiveAnimationDuration: 500,
};

var dataBar = {
labels: [
'Buku',
'Partitur',
'Peta',
'Serial',
'Audio',
'Film'
],
datasets: [{
label: 'Total',
data: [
'{{ isset($total_collection['book']) ? $total_collection['book'] : 0 }}',
'{{ isset($total_collection['partitur']) ? $total_collection['partitur'] : 0 }}',
'{{ isset($total_collection['map']) ? $total_collection['map'] : 0 }}',
'{{ isset($total_collection['serial']) ? $total_collection['serial'] : 0 }}',
'{{ isset($total_collection['audio']) ? $total_collection['audio'] : 0 }}',
'{{ isset($total_collection['film']) ? $total_collection['film'] : 0 }}'
],
backgroundColor: '#17A2B8',
hoverBackgroundColor: '#17A2B8',
borderColor: 'transparent'
}]
};

var dataPie = {
labels: ['Diterima', 'Review', 'Bermasalah', 'PreProcess', 'Ditolak'],
datasets: [{
label: 'Koleksi',
data: [
"{{ isset($collection['collection_accept']) ? $collection['collection_accept'] : 0 }}",
"{{ isset($collection['collection_review']) ? $collection['collection_review'] : 0 }}",
"{{ isset($collection['collection_problem']) ? $collection['collection_problem'] : 0 }}",
"{{ isset($collection['collection_preprocess']) ? $collection['collection_preprocess'] : 0 }}",
"{{ isset($collection['collection_reject']) ? $collection['collection_reject'] : 0 }}",
],
backgroundColor: ['#28A745', '#FFC107', '#DC3545', '#CCC', '#000'],
}]
};

var dataPieKrd = {
labels: @json(array_keys($collection_grouped['grouped']['KRD'])),
datasets: [{
label: 'Koleksi',
data: @json(array_values($collection_grouped['grouped']['KRD'])),
backgroundColor: generateColor(@json(array_values($collection_grouped['grouped']['KRD']))),
}]
};

var dataPieKc = {
labels: @json(array_keys($collection_grouped['grouped']['KC'])),
datasets: [{
label: 'Koleksi',
data: @json(array_values($collection_grouped['grouped']['KC'])),
backgroundColor: generateColor(@json(array_values($collection_grouped['grouped']['KC']))),
}]
};

var dataPieKra = {
labels: @json(array_keys($collection_grouped['grouped']['KRA'])),
datasets: [{
label: 'Koleksi',
data: @json(array_values($collection_grouped['grouped']['KRA'])),
backgroundColor: generateColor(@json(array_values($collection_grouped['grouped']['KRA']))),
}]
};

var dataPieTotal = {
labels: @json(array_keys($collection_grouped['total'])),
datasets: [{
label: 'Koleksi',
data: @json(array_values($collection_grouped['total'])),
backgroundColor: generateColor(@json(array_values($collection_grouped['total']))),
}]
};

var dataPie = {
labels: ['Diterima', 'Review', 'Bermasalah', 'PreProcess', 'Ditolak'],
datasets: [{
label: 'Koleksi',
data: [
"{{ isset($collection['collection_accept']) ? $collection['collection_accept'] : 0 }}",
"{{ isset($collection['collection_review']) ? $collection['collection_review'] : 0 }}",
"{{ isset($collection['collection_problem']) ? $collection['collection_problem'] : 0 }}",
"{{ isset($collection['collection_preprocess']) ? $collection['collection_preprocess'] : 0 }}",
"{{ isset($collection['collection_reject']) ? $collection['collection_reject'] : 0 }}",
],
backgroundColor: ['#28A745', '#FFC107', '#DC3545', '#CCC', '#000'],
}]
};

var dataPie = {
labels: ['Diterima', 'Review', 'Bermasalah', 'PreProcess', 'Ditolak'],
datasets: [{
label: 'Koleksi',
data: [
"{{ isset($collection['collection_accept']) ? $collection['collection_accept'] : 0 }}",
"{{ isset($collection['collection_review']) ? $collection['collection_review'] : 0 }}",
"{{ isset($collection['collection_problem']) ? $collection['collection_problem'] : 0 }}",
"{{ isset($collection['collection_preprocess']) ? $collection['collection_preprocess'] : 0 }}",
"{{ isset($collection['collection_reject']) ? $collection['collection_reject'] : 0 }}",
],
backgroundColor: ['#28A745', '#FFC107', '#DC3545', '#CCC', '#000'],
}]
};

var dataLine = {
labels: [
'Buku',
'Partitur',
'Peta',
'Serial',
'Audio',
'Film'
],
datasets: [{
label: 'Total',
data: [
'{{ isset($collection_last_day['book']) ? $collection_last_day['book'] : 0 }}',
'{{ isset($collection_last_day['partitur']) ? $collection_last_day['partitur'] : 0 }}',
'{{ isset($collection_last_day['map']) ? $collection_last_day['map'] : 0 }}',
'{{ isset($collection_last_day['serial']) ? $collection_last_day['serial'] : 0 }}',
'{{ isset($collection_last_day['audio']) ? $collection_last_day['audio'] : 0 }}',
'{{ isset($collection_last_day['film']) ? $collection_last_day['film'] : 0 }}'
],
backgroundColor: '#17A2B8',
hoverBackgroundColor: '#17A2B8',
borderColor: 'transparent'
}]
};

var dataStackedBar = {
labels: [
'PDF',
'WAV',
'EPUB',
'JFIF',
'MP3',
'PNG',
'JPEG/JPG'
],
datasets: [{
label: 'Total',
data: [
'{{ $file_type['pdf'] }}',
'{{ $file_type['wav'] }}',
'{{ $file_type['epub'] }}',
'{{ $file_type['jfif'] }}',
'{{ $file_type['mp3'] }}',
'{{ $file_type['png'] }}',
parseInt('{{ $file_type['jpeg'] }}') + parseInt('{{ $file_type['jpg'] }}')
],
backgroundColor: '#17A2B8',
hoverBackgroundColor: '#17A2B8',
borderColor: 'transparent'
}]
};

var configPie = {
type: 'pie',
options: optionPie,
data: dataPie
};

var configPieKrd = {
type: 'pie',
options: optionPie,
data: dataPieKrd
};

var configPieKc = {
type: 'pie',
options: optionPie,
data: dataPieKc
};

var configPieKra = {
type: 'pie',
options: optionPie,
data: dataPieKra
};

var configPieTotal = {
type: 'pie',
options: optionPie,
data: dataPieTotal
};

var configBar = {
type: 'bar',
options: optionBar,
data: dataBar
};

var configLine = {
type: 'line',
options: optionLine,
data: dataLine
};

var configStackedBar = {
type: 'horizontalBar',
options: optionStackedBar,
data: dataStackedBar
};

new Chart(selector_bar, configBar);
new Chart(selector_pie, configPie);
new Chart(selector_pie_krd, configPieKrd);
new Chart(selector_pie_kc, configPieKc);
new Chart(selector_pie_kra, configPieKra);
new Chart(selector_pie_total, configPieTotal);
new Chart(selector_line, configLine);
new Chart(selector_stacked_bar, configStackedBar);
