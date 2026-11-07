const reportForm = document.querySelector('[data-review-form]');

if (reportForm) {
    const reviewDialog = reportForm.querySelector('[data-review-dialog]');
    let confirmed = false;

    reportForm.addEventListener('submit', (event) => {
        if (confirmed) {
            return;
        }

        event.preventDefault();
        reportForm.querySelector('[data-review-subject]').textContent = reportForm.elements.title.value;
        reportForm.querySelector('[data-review-location]').textContent = [
            reportForm.elements.category_id.selectedOptions[0]?.textContent,
            reportForm.elements.address.value,
        ].filter(Boolean).join(' · ');
        reportForm.querySelector('[data-review-photo]').textContent = reportForm.elements.photo.files[0]?.name ?? '';
        reviewDialog.showModal();
    });

    reportForm.querySelector('[data-close-review]').addEventListener('click', () => reviewDialog.close());
    reportForm.querySelector('[data-confirm-submit]').addEventListener('click', () => {
        confirmed = true;
        reviewDialog.close();
        reportForm.requestSubmit();
    });

    const description = reportForm.querySelector('[data-character-count]');
    const counter = reportForm.querySelector('[data-count]');
    const updateCount = () => { counter.textContent = String(description.value.length); };
    description.addEventListener('input', updateCount);
    updateCount();

    const photoInput = reportForm.querySelector('[data-photo-input]');
    const photoPreview = reportForm.querySelector('[data-photo-preview]');
    let previewUrl;
    photoInput.addEventListener('change', () => {
        const photo = photoInput.files[0];
        reportForm.querySelector('[data-photo-label]').textContent = photo?.name ?? 'Pilih foto kerusakan';
        if (previewUrl) {
            URL.revokeObjectURL(previewUrl);
        }
        if (photo) {
            previewUrl = URL.createObjectURL(photo);
            photoPreview.src = previewUrl;
            photoPreview.hidden = false;
        } else {
            photoPreview.hidden = true;
        }
    });

    const locationButton = reportForm.querySelector('[data-location-button]');
    const locationMapElement = reportForm.querySelector('[data-location-map]');
    const locationMessage = reportForm.querySelector('[data-location-message]');
    let locationMap;
    let locationMarker;
    let reverseLookupTimer;
    let reverseLookupVersion = 0;

    const lookupAddress = (latitude, longitude, accuracy = null) => {
        const lookupVersion = ++reverseLookupVersion;
        clearTimeout(reverseLookupTimer);
        locationMessage.textContent = 'Mencari nama jalan dan wilayah dari titik peta…';
        reverseLookupTimer = setTimeout(async () => {
            try {
                const parameters = new URLSearchParams({ latitude, longitude });
                const response = await fetch(`${locationButton.dataset.locationEndpoint}?${parameters}`, {
                    headers: { Accept: 'application/json' },
                });
                const result = await response.json().catch(() => ({}));
                if (!response.ok) throw new Error(result.message ?? 'Alamat otomatis belum dapat ditemukan.');
                if (lookupVersion !== reverseLookupVersion) return;

                const filledFields = [];
                const missingFields = [];
                [
                    ['street', reportForm.elements.address, 'nama jalan'],
                    ['village', reportForm.elements.village, 'kelurahan'],
                    ['district', reportForm.elements.district, 'kecamatan'],
                ].forEach(([key, field, label]) => {
                    if (result[key]) {
                        field.value = result[key];
                        filledFields.push(label);
                    } else {
                        missingFields.push(label);
                    }
                });

                const accuracyText = accuracy === null ? '' : ` Akurasi perangkat ±${accuracy} meter.`;
                const filledText = filledFields.length ? `${filledFields.join(', ')} terisi otomatis.` : 'Nama jalan dan wilayah tidak ditemukan.';
                const manualText = missingFields.length ? ` Isi ${missingFields.join(', ')} secara manual.` : ' Periksa atau ubah hasilnya jika perlu.';
                locationMessage.textContent = `${filledText}${manualText}${accuracyText} Pastikan titik berada di lokasi kerusakan.`;
            } catch (error) {
                if (lookupVersion !== reverseLookupVersion) return;
                locationMessage.textContent = `${error.message} Koordinat sudah terisi; Anda dapat mengisi alamat dan wilayah secara manual.`;
            }
        }, 1100);
    };

    const selectMapLocation = (latitude, longitude, accuracy = null) => {
        const point = [latitude, longitude];
        reportForm.elements.latitude.value = latitude.toFixed(7);
        reportForm.elements.longitude.value = longitude.toFixed(7);
        locationMapElement.dataset.latitude = reportForm.elements.latitude.value;
        locationMapElement.dataset.longitude = reportForm.elements.longitude.value;
        if (locationMarker) locationMarker.setLatLng(point);
        if (locationMap) locationMap.panTo(point);
        lookupAddress(reportForm.elements.latitude.value, reportForm.elements.longitude.value, accuracy);
    };

    const initializeLocationMap = () => {
        if (!window.L || locationMap) return;
        locationMapElement.textContent = '';
        const leaflet = window.L;
        const initialLatitude = Number(locationMapElement.dataset.latitude);
        const initialLongitude = Number(locationMapElement.dataset.longitude);
        const hasInitialLocation = Number.isFinite(initialLatitude) && Number.isFinite(initialLongitude);
        const initialPoint = hasInitialLocation ? [initialLatitude, initialLongitude] : [-6.9175, 107.6191];
        locationMap = leaflet.map(locationMapElement, { scrollWheelZoom: false }).setView(initialPoint, hasInitialLocation ? 16 : 12);
        leaflet.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap contributors</a>',
        }).addTo(locationMap);
        locationMarker = leaflet.marker(initialPoint, { draggable: true, keyboard: true }).addTo(locationMap);
        locationMarker.on('dragend', () => {
            const point = locationMarker.getLatLng();
            selectMapLocation(point.lat, point.lng);
        });
        locationMap.on('click', ({ latlng }) => selectMapLocation(latlng.lat, latlng.lng));
    };

    if (window.L) {
        initializeLocationMap();
    } else {
        const leafletScript = document.querySelector('[data-leaflet-script]');
        if (leafletScript) leafletScript.addEventListener('load', initializeLocationMap, { once: true });
        if (leafletScript) {
            leafletScript.addEventListener('error', () => {
                locationMapElement.textContent = 'Peta belum dapat dimuat. Anda tetap dapat mengisi koordinat secara manual.';
            }, { once: true });
        }
    }

    locationButton.addEventListener('click', () => {
        if (!navigator.geolocation) {
            locationMessage.textContent = 'Perangkat tidak mendukung lokasi otomatis. Pilih titik di peta atau isi alamat dan koordinat secara manual.';
            return;
        }

        locationButton.disabled = true;
        locationMessage.textContent = 'Meminta izin lokasi dari browser…';
        navigator.geolocation.getCurrentPosition(({ coords }) => {
            const accuracy = Math.round(coords.accuracy ?? 0);
            selectMapLocation(coords.latitude, coords.longitude, accuracy);
            locationMap?.setView([coords.latitude, coords.longitude], 17);
            locationButton.disabled = false;
        }, (error) => {
            locationMessage.textContent = error.code === error.PERMISSION_DENIED
                ? 'Izin lokasi ditolak. Pilih titik di peta atau isi alamat dan koordinat secara manual.'
                : 'Lokasi tidak dapat diambil. Pilih titik di peta atau isi alamat dan koordinat secara manual.';
            locationButton.disabled = false;
        }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 });
    });
}

document.querySelectorAll('[data-copy-button]').forEach((button) => {
    button.addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(button.dataset.copyButton);
            const label = button.querySelector('[data-copy-label]');
            if (label) label.textContent = 'ID berhasil disalin';
            button.setAttribute('aria-label', 'ID pengaduan berhasil disalin');
        } catch {
            const label = button.querySelector('[data-copy-label]');
            if (label) label.textContent = 'Salin manual: ' + button.dataset.copyButton;
        }
    });
});

const publicMapElement = document.querySelector('[data-public-report-map]');

if (publicMapElement) {
    const mapError = document.querySelector('[data-map-error]');
    const mapEmpty = document.querySelector('[data-map-empty]');
    const reportCount = document.querySelector('[data-report-count]');

    const showMapError = () => {
        mapError.hidden = false;
        reportCount.textContent = '—';
    };

    const initializePublicMap = () => {
        if (!window.L) {
            showMapError();
            return;
        }

        const leaflet = window.L;
        const reportMap = leaflet.map(publicMapElement, { scrollWheelZoom: false }).setView([-6.9175, 107.6191], 12);
        leaflet.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap contributors</a>',
        }).addTo(reportMap);

        const statusColors = {
            pending: '#e49118',
            baru: '#e49118',
            diverifikasi: '#1976d2',
            diproses: '#1565c0',
            selesai: '#2e7d32',
            ditolak: '#7b8794',
        };

        const addPopupText = (parent, tag, text, className = '') => {
            const element = document.createElement(tag);
            if (className) element.className = className;
            element.textContent = text;
            parent.append(element);
            return element;
        };

        const buildPopup = (report) => {
            const popup = document.createElement('article');
            popup.className = 'public-map-popup';
            addPopupText(popup, 'small', report.code, 'popup-code');
            addPopupText(popup, 'h2', report.title);

            const repairClass = report.status === 'selesai' ? 'repair-completed' : 'repair-pending';
            addPopupText(popup, 'span', report.repair_label, `popup-status ${repairClass}`);
            addPopupText(popup, 'span', report.status_label, 'popup-status');
            addPopupText(popup, 'p', report.category);
            addPopupText(popup, 'p', report.description, 'popup-description');
            addPopupText(popup, 'p', `${report.address}, ${report.village}, ${report.district}`, 'popup-address');
            addPopupText(popup, 'p', `Dikirim ${report.reported_at} · ${report.elapsed_label}`, 'popup-time');

            const trackingLink = document.createElement('a');
            trackingLink.className = 'popup-link';
            trackingLink.href = report.tracking_url;
            trackingLink.textContent = 'Lihat detail dan riwayat';
            popup.append(trackingLink);

            return popup;
        };

        fetch(publicMapElement.dataset.endpoint, { headers: { Accept: 'application/json' } })
            .then((response) => {
                if (!response.ok) throw new Error('Peta laporan gagal dimuat.');
                return response.json();
            })
            .then(({ reports, count }) => {
                reportCount.textContent = String(count);
                mapEmpty.hidden = count !== 0;

                const markers = reports.map((report) => {
                    const marker = leaflet.circleMarker([report.latitude, report.longitude], {
                        radius: 9,
                        color: '#ffffff',
                        weight: 2,
                        fillColor: statusColors[report.status] ?? '#64748b',
                        fillOpacity: 0.95,
                        title: `${report.title} — ${report.status_label}`,
                    }).addTo(reportMap);

                    const markerElement = marker.getElement();
                    if (markerElement) {
                        markerElement.setAttribute('tabindex', '0');
                        markerElement.setAttribute('role', 'button');
                        markerElement.setAttribute('aria-label', `${report.title}. ${report.repair_label}. ${report.status_label}.`);
                        markerElement.addEventListener('keydown', (event) => {
                            if (event.key === 'Enter' || event.key === ' ') {
                                event.preventDefault();
                                marker.openPopup();
                            }
                        });
                    }

                    marker.bindPopup(buildPopup(report), { maxWidth: 320 });
                    return marker;
                });

                if (markers.length > 0) {
                    const markerGroup = leaflet.featureGroup(markers);
                    reportMap.fitBounds(markerGroup.getBounds().pad(0.12), { maxZoom: 15 });
                }
            })
            .catch(showMapError);
    };

    if (window.L) {
        initializePublicMap();
    } else {
        const leafletScript = document.querySelector('[data-leaflet-script]');

        if (!leafletScript) {
            showMapError();
        } else {
            leafletScript.addEventListener('load', initializePublicMap, { once: true });
            leafletScript.addEventListener('error', showMapError, { once: true });
        }
    }
}
