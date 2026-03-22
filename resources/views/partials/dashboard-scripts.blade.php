{{-- resources/views/partials/dashboard-scripts.blade.php --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.2/dist/apexcharts.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/js/jsvectormap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/maps/world.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof ApexCharts === 'undefined') {
            console.warn('ApexCharts not loaded');
            const containers = document.querySelectorAll('#candidates_chart, #party_distribution_chart, #projects-overview-chart');
            containers.forEach(container => {
                if (container) {
                    container.innerHTML = `
                        <div class="alert alert-warning text-center p-4">
                            <i class="ri-alert-line fs-1"></i>
                            <h4 class="mt-2">Error al cargar los gráficos</h4>
                            <p class="mb-0">No se pudo cargar la librería de gráficos.</p>
                        </div>
                    `;
                }
            });
            return;
        }
        let ALL_STATS = @json($categoryStats ?? []);
        const LOCALITY_DATA = @json($localityResults ?? []);
        const ACTIVE_CODE = @json($activeCategoryCode ?? '');
        const TOTAL_TABLES = {{ $totalTables ?? 0 }};
        const REPORTED_TABLES = {{ $reportedTables ?? 0 }};
        const CANDIDATE_STATS = @json($candidateStats ?? []);
        let activeCode = ACTIVE_CODE;
        let refreshTimer = null;
        let isRefreshing = false;
        let charts = {};
        const REFRESH_MS = 120000;
        function setText(selector, val) {
            document.querySelectorAll(selector).forEach(el => { el.textContent = val ?? ''; });
        }

        function setRefreshStatus(active) {
            const el = document.getElementById('refresh-status');
            if (el) {
                el.innerHTML = active ? '<small class="text-success">● Activo</small>' : '<small class="text-secondary">○ Pausado</small>';
            }
        }

        function setRefreshIcon(loading) {
            const btn = document.querySelector('.auto-refresh-controls .btn[onclick*="refreshDashboard"]');
            if (!btn) return;
            btn.innerHTML = loading ? '<span class="spinner-border spinner-border-sm" role="status"></span>' : '<i class="ri-refresh-line"></i>';
            btn.disabled = loading;
        }
        function buildArrays(code) {
            const stats = ALL_STATS[code] ?? {};
            const sorted = Object.values(stats.stats ?? {}).sort((a, b) => b.votes - a.votes);
            return {
                names: sorted.map(s => s.candidate?.name ?? 'N/A'),
                shortNames: sorted.map(s => {
                    const party = s.candidate?.party ?? '';
                    if (party.length > 12) {
                        return party.substring(0, 10) + '…';
                    }
                    return party;
                }),
                colors: sorted.map(s => s.candidate?.color ?? '#3b5de7'),
                votes: sorted.map(s => s.votes ?? 0),
                pcts: sorted.map(s => s.percentage ?? 0),
                parties: sorted.map(s => s.candidate?.party ?? ''),
                candidateNames: sorted.map(s => s.candidate?.name ?? 'N/A'),
            };
        }
        function renderBarChart(code) {
            const { shortNames, colors, votes, pcts, candidateNames, parties } = buildArrays(code);
            const el = document.querySelector('#candidates_chart');
            if (!el) return;

            const opts = {
                series: [{ name: 'Votos', data: votes }],
                chart: {
                    type: 'bar',
                    height: 380,
                    toolbar: { show: true },
                    animations: { enabled: true, speed: 400 }
                },
                plotOptions: {
                    bar: {
                        distributed: true,
                        borderRadius: 6,
                        horizontal: false,
                        columnWidth: votes.length > 10 ? '70%' : votes.length > 8 ? '65%' : '55%'
                    }
                },
                xaxis: {
                    categories: shortNames,
                    labels: {
                        show: true,
                        rotate: -35,
                        style: { fontSize: '11px', fontWeight: 500 },
                        formatter: function(value, index) {
                            return value;
                        }
                    },
                    title: { text: 'Partidos', style: { fontSize: '12px' } }
                },
                yaxis: {
                    labels: { formatter: v => v.toLocaleString() },
                    title: { text: 'Votos', style: { fontSize: '12px' } }
                },
                colors: colors,
                dataLabels: {
                    enabled: true,
                    formatter: (val, opts2) => {
                        const pct = pcts[opts2.dataPointIndex];
                        return `${val.toLocaleString()}`;
                    },
                    style: { fontSize: '10px', fontWeight: 'bold', colors: ['#333'] },
                    offsetY: -8,
                    background: { enabled: true, padding: 2, borderRadius: 4 }
                },
                tooltip: {
                    custom: function({ dataPointIndex }) {
                        const party = parties[dataPointIndex];
                        const candidate = candidateNames[dataPointIndex];
                        const votesVal = votes[dataPointIndex];
                        const pct = pcts[dataPointIndex];
                        const color = colors[dataPointIndex];
                        
                        return `<div style="padding: 10px 12px; min-width: 180px;">
                                    <div style="font-weight: bold; margin-bottom: 8px; border-bottom: 1px solid #eee; padding-bottom: 5px;">
                                        <span style="color: ${color};">●</span> ${party}
                                    </div>
                                    <div style="margin-bottom: 5px;">
                                        <span style="color: #666;">Candidato:</span> 
                                        <strong>${candidate}</strong>
                                    </div>
                                    <div style="margin-bottom: 5px;">
                                        <span style="color: #666;">Votos:</span> 
                                        <strong>${votesVal.toLocaleString()}</strong>
                                    </div>
                                    <div>
                                        <span style="color: #666;">Porcentaje:</span> 
                                        <strong style="color: ${color};">${pct}%</strong>
                                    </div>
                                </div>`;
                    }
                },
                grid: { borderColor: '#e9ecef', padding: { top: 20 } },
                legend: { show: false }
            };

            if (charts.bar) {
                charts.bar.updateOptions({ colors, xaxis: { categories: shortNames } });
                charts.bar.updateSeries([{ name: 'Votos', data: votes }]);
            } else {
                charts.bar = new ApexCharts(el, opts);
                charts.bar.render();
            }
        }
        function renderDonut(code) {
            const { shortNames, colors, votes, pcts, parties, candidateNames } = buildArrays(code);
            const el = document.querySelector('#party_distribution_chart');
            if (!el || !votes.length) return;

            const opts = {
                series: votes,
                labels: shortNames,
                colors: colors,
                chart: {
                    type: 'donut',
                    height: 320,
                    animations: { enabled: true, speed: 400 }
                },
                legend: {
                    position: 'bottom',
                    fontSize: '11px',
                    itemMargin: { horizontal: 8, vertical: 4 },
                    formatter: function(seriesName, opts) {
                        const index = opts.seriesIndex;
                        const votesVal = votes[index];
                        const pct = pcts[index];
                        return `<div class="d-flex align-items-center justify-content-between w-100">
                                    <span>${seriesName}</span>
                                    <span class="ms-2 text-muted">${votesVal.toLocaleString()} (${pct}%)</span>
                                </div>`;
                    }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '60%',
                            labels: {
                                show: true,
                                value: {
                                    fontSize: '18px',
                                    fontWeight: 700,
                                    formatter: v => Number(v).toLocaleString()
                                },
                                total: {
                                    show: true,
                                    label: 'Total Votos',
                                    fontSize: '12px',
                                    formatter: w => {
                                        const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                        return total.toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                },
                tooltip: {
                    custom: function({ seriesIndex }) {
                        const party = parties[seriesIndex];
                        const candidate = candidateNames[seriesIndex];
                        const votesVal = votes[seriesIndex];
                        const pct = pcts[seriesIndex];
                        const color = colors[seriesIndex];
                        
                        return `<div style="padding: 10px 12px; min-width: 180px;">
                                    <div style="font-weight: bold; margin-bottom: 8px; border-bottom: 1px solid #eee; padding-bottom: 5px;">
                                        <span style="color: ${color};">●</span> ${party}
                                    </div>
                                    <div style="margin-bottom: 5px;">
                                        <span style="color: #666;">Candidato:</span> 
                                        <strong>${candidate}</strong>
                                    </div>
                                    <div style="margin-bottom: 5px;">
                                        <span style="color: #666;">Votos:</span> 
                                        <strong>${votesVal.toLocaleString()}</strong>
                                    </div>
                                    <div>
                                        <span style="color: #666;">Porcentaje:</span> 
                                        <strong style="color: ${color};">${pct}%</strong>
                                    </div>
                                </div>`;
                    }
                },
                dataLabels: { enabled: false }
            };

            if (charts.donut) {
                charts.donut.updateOptions({ labels: shortNames, colors });
                charts.donut.updateSeries(votes);
            } else {
                charts.donut = new ApexCharts(el, opts);
                charts.donut.render();
            }
        }
        function renderRadial(reported, total) {
            const el = document.querySelector('#progress-radial-chart');
            if (!el) return;
            const pct = total > 0 ? Math.round((reported / total) * 100) : 0;
            const opts = {
                series: [pct],
                chart: { type: 'radialBar', height: 200, animations: { enabled: true } },
                plotOptions: {
                    radialBar: {
                        hollow: { size: '55%' },
                        dataLabels: {
                            name: { show: true, fontSize: '13px', offsetY: -8, formatter: () => 'Escrutadas' },
                            value: { show: true, fontSize: '22px', offsetY: 4, formatter: v => v + '%' }
                        }
                    }
                },
                colors: [pct >= 75 ? '#0ab39c' : pct >= 50 ? '#f7b84b' : '#f06548'],
            };
            if (charts.radial) {
                charts.radial.updateSeries([pct]);
            } else {
                charts.radial = new ApexCharts(el, opts);
                charts.radial.render();
            }
        }

        function renderLocalityChart(code) {
            const el = document.querySelector('#projects-overview-chart');
            if (!el) return;

            const localities = Object.values(LOCALITY_DATA);
            if (!localities.length) return;

            const localityNames = localities.map(l => l.name ?? '?');
            const { shortNames, colors, votes, parties } = buildArrays(code);

            const series = shortNames.map((name, idx) => ({
                name: name,
                data: localities.map(l => {
                    const cat = Object.values(l.categories ?? {}).find(c =>
                        (c.candidates ?? []).some(x => {
                            const partyName = x.party ?? '';
                            return partyName === name || partyName === name;
                        })
                    );
                    if (!cat) return 0;
                    const cand = cat.candidates.find(x => {
                        const partyName = x.party ?? '';
                        return partyName === name;
                    });
                    return cand?.votes ?? 0;
                }),
            }));

            const opts = {
                series: series,
                chart: { type: 'bar', height: 300, stacked: false, toolbar: { show: false }, animations: { enabled: true } },
                xaxis: { categories: localityNames, labels: { rotate: -35, style: { fontSize: '11px' } } },
                yaxis: { labels: { formatter: v => v.toLocaleString() } },
                colors: colors,
                plotOptions: { bar: { columnWidth: '75%', borderRadius: 3 } },
                legend: { position: 'bottom', fontSize: '11px' },
                tooltip: { 
                    shared: true, 
                    intersect: false, 
                    y: { 
                        formatter: (val, { seriesIndex }) => {
                            return `${val.toLocaleString()} votos - ${shortNames[seriesIndex]}`;
                        }
                    } 
                },
            };

            if (charts.locality) {
                charts.locality.updateOptions({ colors, xaxis: { categories: localityNames } });
                charts.locality.updateSeries(series);
            } else {
                charts.locality = new ApexCharts(el, opts);
                charts.locality.render();
            }
        }
        function initMap() {
            const mapEl = document.getElementById('votes-by-locations');
            if (!mapEl || !Object.keys(LOCALITY_DATA).length) return;
            if (typeof jsVectorMap === 'undefined') return;
            const markers = Object.values(LOCALITY_DATA).map(l => ({
                name: `${l.name} (${l.total_votes ?? 0} votos)`,
                coords: [l.latitude ?? -17.4, l.longitude ?? -66.2],
                votes: l.total_votes ?? 0,
                categories: l.categories ?? {},
            }));

            mapEl.innerHTML = '';
            charts.map = new jsVectorMap({
                map: 'world',
                selector: '#votes-by-locations',
                zoomOnScroll: true,
                zoomButtons: true,
                markers: markers,
                markerStyle: {
                    initial: { fill: '#0ab39c' },
                    hover: { fill: '#f06548' },
                    selected: { fill: '#f06548' },
                },
                onMarkerClick: function(event, index) { showMarkerPopup(markers[index]); },
            });
        }

        function showMarkerPopup(marker) {
            document.querySelector('.custom-map-popup')?.remove();
            let rows = '';
            Object.entries(marker.categories).forEach(([code, cat]) => {
                rows += `<li class="list-group-item list-group-item-secondary small fw-bold">${cat.label ?? code}</li>`;
                (cat.candidates ?? []).forEach(c => {
                    rows += `
                        <li class="list-group-item d-flex justify-content-between align-items-center py-1">
                            <span class="small">${c.name} <span class="text-muted">(${c.party})</span></span>
                            <span class="badge bg-primary rounded-pill">${(c.votes ?? 0).toLocaleString()} · ${c.percentage ?? 0}%</span>
                        </li>`;
                });
            });
            if (!rows) rows = '<li class="list-group-item small text-muted">Sin datos</li>';

            const popup = document.createElement('div');
            popup.className = 'custom-map-popup position-fixed top-50 start-50 translate-middle bg-white rounded shadow-lg';
            popup.style.cssText = 'z-index:10000;width:340px;max-width:92vw;';
            popup.innerHTML = `
                <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                    <h6 class="mb-0 fw-bold">${marker.name}</h6>
                    <button type="button" class="btn-close btn-close-sm"></button>
                </div>
                <div class="p-2" style="max-height:60vh;overflow-y:auto;">
                    <ul class="list-group list-group-flush">${rows}</ul>
                </div>`;
            popup.querySelector('.btn-close').addEventListener('click', () => popup.remove());
            document.body.appendChild(popup);
        }

        // loadInstitutionTables defined globally below (outside DOMContentLoaded)

        // printModalContent defined globally below (outside DOMContentLoaded)

        window.exportModalToCSV = function() {
            const modalContent = document.getElementById('modalTablesContent');
            const table = modalContent?.querySelector('table');
            if (!table) {
                alert('No hay datos para exportar');
                return;
            }
            const institutionName = document.getElementById('modalInstitutionName').innerText;
            const rows = [];
            const headers = [];
            table.querySelectorAll('thead th').forEach(th => {
                headers.push(th.innerText.trim());
            });
            rows.push(headers);
            table.querySelectorAll('tbody tr').forEach(tr => {
                const row = [];
                tr.querySelectorAll('td').forEach(td => {
                    row.push(td.innerText.trim());
                });
                if (row.length) rows.push(row);
            });
            const csvContent = rows.map(row =>
                row.map(cell => `"${String(cell).replace(/"/g, '""')}"`).join(',')
            ).join('\n');
            const blob = new Blob(["\uFEFF" + csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.href = url;
            link.setAttribute('download', `${institutionName.replace(/[^a-z0-9]/gi, '_')}_mesas.csv`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        };

        window.exportModalToImage = function() {
            const modalContent = document.getElementById('modalTablesContent');
            const institutionName = document.getElementById('modalInstitutionName').innerText;
            if (!modalContent) {
                alert('No hay datos para exportar');
                return;
            }
            if (typeof html2canvas === 'undefined') {
                alert('Cargando librería de imágenes, por favor intente nuevamente');
                const script = document.createElement('script');
                script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js';
                script.onload = () => setTimeout(() => window.exportModalToImage(), 500);
                document.body.appendChild(script);
                return;
            }
            const container = document.createElement('div');
            container.style.position = 'fixed';
            container.style.top = '-9999px';
            container.style.left = '-9999px';
            container.style.backgroundColor = 'white';
            container.style.padding = '16px';
            container.style.width = '500px';
            container.style.fontFamily = 'Arial, sans-serif';
            container.style.borderRadius = '12px';
            const header = document.createElement('div');
            header.style.textAlign = 'center';
            header.style.marginBottom = '16px';
            header.style.padding = '12px';
            header.style.backgroundColor = '#f8f9fa';
            header.style.borderRadius = '8px';
            header.innerHTML = `
                <h3 style="margin: 0 0 4px 0; font-size: 18px; color: #0ab39c;">${institutionName}</h3>
                <p style="margin: 0; font-size: 10px; color: #666;">Reporte de Mesas</p>
                <p style="margin: 4px 0 0 0; font-size: 9px; color: #999;">${new Date().toLocaleString('es-BO')}</p>
            `;
            container.appendChild(header);
            const contentClone = modalContent.cloneNode(true);
            container.appendChild(contentClone);
            const footer = document.createElement('div');
            footer.style.textAlign = 'center';
            footer.style.marginTop = '12px';
            footer.style.paddingTop = '8px';
            footer.style.borderTop = '1px solid #eee';
            footer.style.fontSize = '8px';
            footer.style.color = '#999';
            footer.innerHTML = `Sistema Electoral | ${new Date().toLocaleString('es-BO')}`;
            container.appendChild(footer);
            document.body.appendChild(container);
            setTimeout(() => {
                html2canvas(container, {
                    scale: 2.5,
                    backgroundColor: '#ffffff',
                    logging: false,
                    useCORS: true
                }).then(canvas => {
                    const link = document.createElement('a');
                    const timestamp = new Date().toISOString().slice(0,19).replace(/:/g, '-');
                    link.download = `${institutionName.replace(/[^a-z0-9]/gi, '_')}_mesas_${timestamp}.png`;
                    link.href = canvas.toDataURL('image/png');
                    link.click();
                    const preview = canvas.toDataURL('image/png');
                    console.log('Image ready for sharing:', preview.substring(0, 100) + '...');
                    document.body.removeChild(container);
                    const successMsg = document.createElement('div');
                    successMsg.style.position = 'fixed';
                    successMsg.style.bottom = '20px';
                    successMsg.style.right = '20px';
                    successMsg.style.backgroundColor = '#28a745';
                    successMsg.style.color = 'white';
                    successMsg.style.padding = '8px 16px';
                    successMsg.style.borderRadius = '8px';
                    successMsg.style.fontSize = '12px';
                    successMsg.style.zIndex = '9999';
                    successMsg.innerHTML = '<i class="ri-check-line"></i> Imagen guardada';
                    document.body.appendChild(successMsg);
                    setTimeout(() => successMsg.remove(), 2000);
                }).catch(error => {
                    console.error('Error:', error);
                    alert('Error al generar la imagen');
                    document.body.removeChild(container);
                });
            }, 500);
        };

        // exportSeatsToImage defined globally below (outside DOMContentLoaded)

        window.printModalContent = function() {
            const modalContent = document.getElementById('modalTablesContent');
            const institutionName = document.getElementById('modalInstitutionName').innerText;
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>${institutionName} - Mesas</title>
                </head>
                <body>
                    <div class="header">
                        <h1>${institutionName}</h1>
                        <p>Reporte de mesas - Generado: ${new Date().toLocaleString('es-BO')}</p>
                    </div>
                    ${modalContent.cloneNode(true).innerHTML}
                    <div class="footer">
                        <p>Documento generado por Sistema Electoral</p>
                    </div>
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
        };
        window.exportSeatsToCSV = function() {
            let seatTable = document.querySelector('#dashboard-seats table');
            if (!seatTable) {
                alert('No se encontró la tabla de concejales para exportar');
                return;
            }
            const mode = document.querySelector('#currentSeatMode')?.value || 'Preliminar';
            const rows = [];
            rows.push(['Distribución de Concejales - Método D\'Hondt']);
            rows.push([`Modo: ${mode}`]);
            rows.push([`Fecha: ${new Date().toLocaleString('es-BO')}`]);
            rows.push([]);
            const headers = [];
            seatTable.querySelectorAll('thead th').forEach(th => {
                headers.push(th.innerText.trim());
            });
            rows.push(headers);
            seatTable.querySelectorAll('tbody tr').forEach(tr => {
                const row = [];
                tr.querySelectorAll('td').forEach(td => {
                    row.push(td.innerText.trim());
                });
                if (row.length) rows.push(row);
            });
            rows.push([]);
            rows.push(['* Escaños calculados sobre votos válidos (excluye blancos y nulos)']);
            rows.push([`Generado: ${new Date().toLocaleString('es-BO')}`]);
            const csvContent = rows.map(row => 
                row.map(cell => `"${String(cell).replace(/"/g, '""')}"`).join(',')
            ).join('\n');
            const blob = new Blob(["\uFEFF" + csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.href = url;
            link.setAttribute('download', `concejales_${mode}_${new Date().toISOString().slice(0,19).replace(/:/g, '-')}.csv`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
            showSuccessMessage('CSV exportado correctamente');
        };
        function showSuccessMessage(message) {
            const msg = document.createElement('div');
            msg.style.position = 'fixed';
            msg.style.bottom = '20px';
            msg.style.right = '20px';
            msg.style.backgroundColor = '#28a745';
            msg.style.color = 'white';
            msg.style.padding = '8px 16px';
            msg.style.borderRadius = '8px';
            msg.style.fontSize = '12px';
            msg.style.zIndex = '9999';
            msg.innerHTML = `<i class="ri-check-line"></i> ${message}`;
            document.body.appendChild(msg);
            setTimeout(() => msg.remove(), 2000);
        }
        window.exportSeatsToImage = function() {
            let seatsCard = document.querySelector('#dashboard-seats');    
            if (!seatsCard) {
                seatsCard = document.querySelector('.card:has(.card-title)');
            }    
            if (!seatsCard) {
                alert('No se encontró la tabla de concejales para exportar');
                return;
            }
            const mode = document.querySelector('#currentSeatMode')?.value || 'Preliminar';
            if (typeof html2canvas === 'undefined') {
                alert('Cargando librería de imágenes, por favor intente nuevamente en unos segundos');
                const script = document.createElement('script');
                script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js';
                script.onload = () => {
                    setTimeout(() => window.exportSeatsToImage(), 500);
                };
                document.body.appendChild(script);
                return;
            }
            const container = document.createElement('div');
            container.style.position = 'fixed';
            container.style.top = '-9999px';
            container.style.left = '-9999px';
            container.style.backgroundColor = 'white';
            container.style.padding = '20px';
            container.style.width = '800px';
            container.style.fontFamily = 'Arial, sans-serif';
            container.style.borderRadius = '8px';
            const header = document.createElement('div');
            header.style.textAlign = 'center';
            header.style.marginBottom = '20px';
            header.style.padding = '15px';
            header.style.backgroundColor = '#f8f9fa';
            header.style.borderRadius = '8px';
            header.innerHTML = `
                <h3 style="margin: 0 0 5px 0; color: #0ab39c;">Distribución de Concejales</h3>
                <p style="margin: 0; color: #666; font-size: 12px;">Método D'Hondt</p>
                <p style="margin: 5px 0 0 0; color: #999; font-size: 11px;">Modo: ${mode}</p>
                <p style="margin: 2px 0 0 0; color: #999; font-size: 10px;">${new Date().toLocaleString('es-BO')}</p>
            `;
            container.appendChild(header);
            const table = seatsCard.querySelector('table');
            if (table) {
                const tableClone = table.cloneNode(true);
                tableClone.querySelectorAll('.btn, button, .progress-bar-animated').forEach(el => {
                    if (el.classList && el.classList.contains('progress-bar')) {
                        el.classList.remove('progress-bar-animated');
                    } else {
                        const text = document.createTextNode(el.textContent || '');
                        el.parentNode.replaceChild(text, el);
                    }
                });
                container.appendChild(tableClone);
            } else {
                container.innerHTML += '<p class="text-center text-muted">No hay datos disponibles</p>';
            }
            const footer = document.createElement('div');
            footer.style.textAlign = 'center';
            footer.style.marginTop = '15px';
            footer.style.paddingTop = '10px';
            footer.style.borderTop = '1px solid #eee';
            footer.style.fontSize = '9px';
            footer.style.color = '#999';
            footer.innerHTML = `* Escaños calculados sobre votos válidos (excluye blancos y nulos)`;
            container.appendChild(footer);    
            document.body.appendChild(container);
            setTimeout(() => {
                html2canvas(container, {
                    scale: 2,
                    backgroundColor: '#ffffff',
                    logging: false,
                    useCORS: true
                }).then(canvas => {
                    const link = document.createElement('a');
                    const timestamp = new Date().toISOString().slice(0,19).replace(/:/g, '-');
                    link.download = `concejales_${mode}_${timestamp}.png`;
                    link.href = canvas.toDataURL('image/png');
                    link.click();
                    document.body.removeChild(container);
                    const modal = document.getElementById('seatExportModal');
                    if (modal && modal.classList.contains('show')) {
                        const bsModal = bootstrap.Modal.getInstance(modal);
                        if (bsModal) bsModal.hide();
                    }
                }).catch(error => {
                    console.error('Error:', error);
                    alert('Error al generar la imagen');
                    document.body.removeChild(container);
                });
            }, 500);
        };
        window.printSeatsTable = function() {
            let seatsCard = document.querySelector('#dashboard-seats');
            if (!seatsCard) {
                alert('No se encontró la tabla de concejales para imprimir');
                return;
            }
            const mode = document.querySelector('#currentSeatMode')?.value || 'Preliminar';
            const printWindow = window.open('', '_blank');

            const contentClone = seatsCard.cloneNode(true);
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Distribución de Concejales - ${mode}</title>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            padding: 20px;
                            margin: 0;
                        }
                        .header {
                            text-align: center;
                            margin-bottom: 30px;
                            border-bottom: 2px solid #333;
                            padding-bottom: 10px;
                        }
                        .header h1 {
                            font-size: 24px;
                            margin-bottom: 5px;
                        }
                        .header p {
                            color: #666;
                            font-size: 12px;
                            margin: 5px 0;
                        }
                        table {
                            width: 100%;
                            border-collapse: collapse;
                            margin-top: 20px;
                        }
                        th, td {
                            border: 1px solid #ddd;
                            padding: 8px;
                            text-align: center;
                        }
                        th {
                            background-color: #f5f5f5;
                            font-weight: bold;
                        }
                        .badge {
                            display: inline-block;
                            padding: 2px 6px;
                            border-radius: 4px;
                            font-size: 11px;
                        }
                        .bg-success { background: #28a745; color: white; }
                        .bg-primary { background: #0ab39c; color: white; }
                        .bg-warning { background: #ffc107; color: black; }
                        .bg-danger { background: #dc3545; color: white; }
                        .progress {
                            height: 6px;
                            background: #e9ecef;
                            border-radius: 3px;
                            overflow: hidden;
                        }
                        .progress-bar {
                            height: 100%;
                            background: #0ab39c;
                        }
                        .footer {
                            margin-top: 30px;
                            text-align: center;
                            font-size: 10px;
                            color: #666;
                            border-top: 1px solid #ddd;
                            padding-top: 10px;
                        }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>Distribución de Concejales - Método D'Hondt</h1>
                        <p>Modo: ${mode}</p>
                        <p>Fecha: ${new Date().toLocaleString('es-BO')}</p>
                        <p><small>Escaños calculados sobre votos válidos (excluye blancos y nulos)</small></p>
                    </div>
                    ${contentClone.innerHTML}
                    <div class="footer">
                        <p>Documento generado por Sistema Electoral</p>
                    </div>
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
        };
        window.filterLocality = function(localityId) {
            document.querySelectorAll('.locality-progress-item').forEach(item => {
                item.style.display = (localityId === 'all' || item.dataset.localityId == localityId) ? '' : 'none';
            });
            document.querySelectorAll('#locality-filter-btns button').forEach(btn => {
                btn.classList.toggle('active', btn.textContent.trim() === 'Todos' ? localityId === 'all' : false);
            });
        };
        window.exportTableToCSV = function(tableId, filename) {
            const table = document.getElementById(tableId);
            if (!table) return;
            const rows = [...table.querySelectorAll('tr')].map(row =>
                [...row.querySelectorAll('td,th')].map(cell => '"' + cell.innerText.replace(/\n/g, ' ').replace(/"/g, '""') + '"').join(',')
            );
            const blob = new Blob([rows.join('\n')], { type: 'text/csv;charset=utf-8;' });
            const link = Object.assign(document.createElement('a'), {
                href: URL.createObjectURL(blob),
                download: filename,
                style: 'display:none',
            });
            document.body.append(link);
            link.click();
            link.remove();
        };
        window.switchChartCategory = function(code) {
            activeCode = code;
            renderBarChart(code);
            renderDonut(code);
            renderLocalityChart(code);
        };
        function refreshDashboard() {
            if (isRefreshing) return;
            isRefreshing = true;
            setRefreshIcon(true);
            const electionType = document.querySelector('select[name="election_type"]')?.value || '';
            const category     = document.getElementById('filter-category-input')?.value || '';
            const department   = document.getElementById('dept-select')?.value || '';
            const province     = document.getElementById('prov-select')?.value || '';
            const municipality = document.getElementById('muni-select')?.value || '';
            const params = new URLSearchParams({ election_type: electionType, category, department, province, municipality });
            fetch(`/refresh-dashboard?${params}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => {
                if (!r.ok) throw new Error(`HTTP ${r.status}`);
                return r.json();
            })
            .then(data => {
                if (!data.success) throw new Error(data.message ?? 'Error del servidor');

                const pending = (data.totalTables ?? 0) - (data.reportedTables ?? 0);

                // --- KPI cards (dashboard-content) ---
                setText('#kpi-reported',   data.reportedTables);
                setText('#kpi-total',      data.totalTables);
                setText('#kpi-pending',    pending);
                setText('#kpi-votes',      Number(data.totalVotes).toLocaleString());
                setText('#kpi-blank',      Number(data.totalBlankVotes).toLocaleString());
                setText('#kpi-null',       Number(data.totalNullVotes).toLocaleString());
                setText('#kpi-pct',        data.progressPercentage + '%');
                const kpiBar = document.getElementById('kpi-bar');
                if (kpiBar) kpiBar.style.width = data.progressPercentage + '%';

                // --- Stat blocks (dashboard-tables-stats / dashboard-content duplicates) ---
                setText('.reported-tables-count, #ds-stat-reported, #ds-stat-reported2', data.reportedTables);
                setText('.pending-tables-count,  #ds-stat-pending,  #ds-stat-pending2',  pending);
                setText('.total-tables-count,    #ds-stat-total,    #ds-stat-total2',    data.totalTables);
                setText('#ds-stat-pct',    data.progressPercentage);
                const bigBar = document.getElementById('ds-big-bar');
                if (bigBar) bigBar.style.width = data.progressPercentage + '%';

                // --- Progress-general section ---
                const genBar = document.querySelector('.general-progress-bar');
                if (genBar) {
                    genBar.style.width = data.progressPercentage + '%';
                    genBar.textContent = data.progressPercentage + '%';
                }
                const progText = document.querySelector('.progress-text');
                if (progText) progText.textContent = `${data.reportedTables} de ${data.totalTables} mesas reportadas`;

                // --- Timestamp in filter bar ---
                const timeEl = document.getElementById('ds-filter-time');
                if (timeEl && data.last_updated) {
                    timeEl.textContent = data.last_updated.slice(11, 16); // HH:mm
                }

                // --- Re-render charts if backend returned fresh categoryStats ---
                if (data.categoryStats && Object.keys(data.categoryStats).length) {
                    ALL_STATS = data.categoryStats;
                    renderBarChart(activeCode);
                    renderDonut(activeCode);
                    renderLocalityChart(activeCode);
                }

                renderRadial(data.reportedTables, data.totalTables);
            })
            .catch(err => {
                // Never reload — just log and show a brief toast
                console.warn('Refresh error:', err.message);
                const toast = document.createElement('div');
                toast.style.cssText = 'position:fixed;bottom:70px;right:20px;z-index:9999;background:#dc3545;color:#fff;padding:6px 14px;border-radius:6px;font-size:12px;';
                toast.textContent = '⚠ Sin conexión, reintentando…';
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 3000);
            })
            .finally(() => {
                isRefreshing = false;
                setRefreshIcon(false);
            });
        }

        function startAutoRefresh() {
            if (refreshTimer) clearInterval(refreshTimer);
            refreshTimer = setInterval(refreshDashboard, REFRESH_MS);
            setRefreshStatus(true);
        }
        function stopAutoRefresh() {
            if (refreshTimer) {
                clearInterval(refreshTimer);
                refreshTimer = null;
            }
            setRefreshStatus(false);
        }
        if (Object.keys(ALL_STATS).length) {
            renderBarChart(activeCode);
            renderDonut(activeCode);
            renderLocalityChart(activeCode);
        }
        renderRadial(REPORTED_TABLES, TOTAL_TABLES);
        initMap();
        window.refreshDashboard = refreshDashboard;
        window.startAutoRefresh = startAutoRefresh;
        window.stopAutoRefresh = stopAutoRefresh;
        window.ElectionDashboard = {
            refresh: refreshDashboard,
            startAuto: startAutoRefresh,
            stopAuto: stopAutoRefresh
        };
        startAutoRefresh();
    });
    window.openSeatExportModal = function() {
        const modalElement = document.getElementById('seatExportModal');
        if (!modalElement) return;    
        modalElement.style.display = 'block';
        modalElement.classList.add('show');
        document.body.style.overflow = 'hidden';    
        if (!document.querySelector('.modal-backdrop')) {
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            backdrop.style.position = 'fixed';
            backdrop.style.top = '0';
            backdrop.style.left = '0';
            backdrop.style.width = '100%';
            backdrop.style.height = '100%';
            backdrop.style.backgroundColor = 'rgba(0,0,0,0.5)';
            backdrop.style.zIndex = '1040';
            document.body.appendChild(backdrop);
            backdrop.onclick = function() {
                closeSeatExportModal();
            };
        }
    };
    window.closeSeatExportModal = function() {
        const modalElement = document.getElementById('seatExportModal');
        if (!modalElement) return;    
        modalElement.style.display = 'none';
        modalElement.classList.remove('show');
        document.body.style.overflow = '';
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) backdrop.remove();
    };
    window.closeInstitutionModal = function() {
        const modalElement = document.getElementById('institutionTablesModal');
        if (!modalElement) return;    
        modalElement.style.display = 'none';
        modalElement.classList.remove('show');
        document.body.style.overflow = '';    
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) backdrop.remove();
    };
    window.loadInstitutionTables = function(institutionId, institutionName) {
        const modalElement = document.getElementById('institutionTablesModal');
        const modalTitle = document.getElementById('modalInstitutionName');
        const modalContent = document.getElementById('modalTablesContent');    
        if (!modalElement) return;
        modalTitle.innerHTML = institutionName;
        modalContent.innerHTML = `
            <div class="text-center p-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2 text-muted">Cargando mesas...</p>
            </div>
        `;    
        modalElement.style.display = 'block';
        modalElement.classList.add('show');
        document.body.style.overflow = 'hidden';
        if (!document.querySelector('.modal-backdrop')) {
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            backdrop.style.position = 'fixed';
            backdrop.style.top = '0';
            backdrop.style.left = '0';
            backdrop.style.width = '100%';
            backdrop.style.height = '100%';
            backdrop.style.backgroundColor = 'rgba(0,0,0,0.5)';
            backdrop.style.zIndex = '1040';
            document.body.appendChild(backdrop);
            backdrop.onclick = () => closeInstitutionModal();
        }    
        const electionType = document.querySelector('select[name="election_type"]')?.value || '';
        fetch(`/institutions/${institutionId}/tables?election_type=${electionType}`)
            .then(response => response.text())
            .then(html => {
                modalContent.innerHTML = html;
            })
            .catch(error => {
                modalContent.innerHTML = `
                    <div class="alert alert-danger m-3">
                        <i class="ri-error-warning-line me-2"></i>
                        Error: ${error.message}
                    </div>
                `;
            });
    };
    window.copySeatsToClipboard = function() {
        const seatTable = document.querySelector('#dashboard-seats table');
        if (!seatTable) {
            alert('No hay datos para copiar');
            return;
        }    
        let text = '';
        const mode = document.querySelector('#currentSeatMode')?.value || 'Preliminar';
        text += `Distribución de Concejales - Método D'Hondt\n`;
        text += `Modo: ${mode}\n`;
        text += `Fecha: ${new Date().toLocaleString('es-BO')}\n\n`;
        const headers = [];
        seatTable.querySelectorAll('thead th').forEach(th => {
            headers.push(th.innerText.trim());
        });
        text += headers.join('\t') + '\n';
        seatTable.querySelectorAll('tbody tr').forEach(tr => {
            const row = [];
            tr.querySelectorAll('td').forEach(td => {
                row.push(td.innerText.trim());
            });
            if (row.length) text += row.join('\t') + '\n';
        });
        text += `\n* Escaños calculados sobre votos válidos (excluye blancos y nulos)`;
        text += `\nGenerado: ${new Date().toLocaleString('es-BO')}`;
        navigator.clipboard.writeText(text).then(() => {
            const successMsg = document.createElement('div');
            successMsg.style.position = 'fixed';
            successMsg.style.bottom = '20px';
            successMsg.style.right = '20px';
            successMsg.style.backgroundColor = '#28a745';
            successMsg.style.color = 'white';
            successMsg.style.padding = '8px 16px';
            successMsg.style.borderRadius = '8px';
            successMsg.style.fontSize = '12px';
            successMsg.style.zIndex = '9999';
            successMsg.innerHTML = '<i class="ri-check-line"></i> Copiado al portapapeles';
            document.body.appendChild(successMsg);
            setTimeout(() => successMsg.remove(), 2000);
        }).catch(err => {
            alert('Error al copiar: ' + err.message);
        });
    };
    document.querySelectorAll('.dropdown-toggle').forEach(dropdown => {
        if (typeof bootstrap !== 'undefined') {
            new bootstrap.Dropdown(dropdown);
        }
    });
</script>