import './bootstrap';
import Alpine from 'alpinejs';
import ApexCharts from 'apexcharts';

window.Alpine = Alpine;
window.ApexCharts = ApexCharts;

Alpine.data('adminShell', () => ({
    sidebarOpen: window.innerWidth < 1024,
}));

Alpine.data('confirmButton', (message = 'Lanjutkan tindakan ini?') => ({
    message,
    submit(event) {
        if (!window.confirm(this.message)) {
            event.preventDefault();
        }
    },
}));

Alpine.data('bookingForm', (config) => ({
    submitting: false,
    packageId: String(config.initial.packageId || ''),
    unitId: String(config.initial.unitId || ''),
    bookingDate: config.initial.bookingDate,
    startTime: config.initial.startTime,
    packages: config.packages,
    units: config.units,
    quote: null,
    availableUnitIds: [],
    availabilityLoaded: false,
    availabilityLoading: false,
    availabilityError: '',
    requestVersion: 0,

    get selectedPackage() {
        return this.packages.find((item) => String(item.id) === this.packageId) || null;
    },

    get selectedUnit() {
        return this.units.find((item) => String(item.id) === this.unitId) || null;
    },

    formatCurrency(value) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0,
        }).format(Number(value || 0));
    },

    isUnitAvailable(id) {
        if (!this.availabilityLoaded) return String(id) === this.unitId;
        return this.availableUnitIds.map(String).includes(String(id));
    },

    async refreshAvailability() {
        const version = ++this.requestVersion;
        this.quote = null;
        this.availabilityLoaded = false;
        this.availabilityError = '';

        if (!this.packageId || !this.bookingDate || !this.startTime) {
            this.availableUnitIds = [];
            if (this.unitId) this.unitId = '';
            return;
        }

        this.availabilityLoading = true;

        try {
            const payload = {
                package_id: this.packageId,
                booking_date: this.bookingDate,
                start_time: this.startTime,
            };
            const [quoteResponse, availabilityResponse] = await Promise.all([
                axios.post(config.quoteUrl, payload),
                axios.post(config.availabilityUrl, payload),
            ]);

            if (version !== this.requestVersion) return;

            this.quote = quoteResponse.data;
            this.availableUnitIds = availabilityResponse.data.data.map((unit) => unit.id);
            this.availabilityLoaded = true;

            if (this.unitId && !this.isUnitAvailable(this.unitId)) {
                this.unitId = '';
            }
        } catch (error) {
            if (version !== this.requestVersion) return;

            const errors = error.response?.data?.errors || {};
            const firstError = Object.values(errors)[0];
            this.availabilityError = Array.isArray(firstError)
                ? firstError[0]
                : 'Jadwal atau paket yang dipilih belum dapat diproses.';
            this.availableUnitIds = [];
            this.availabilityLoaded = true;
        } finally {
            if (version === this.requestVersion) this.availabilityLoading = false;
        }
    },

    init() {
        this.$nextTick(() => this.refreshAvailability());
    },
}));

const renderCharts = () => {
    document.querySelectorAll('[data-chart]').forEach((element) => {
        if (element.dataset.rendered === 'true') return;

        try {
            const options = JSON.parse(element.dataset.chart);
            const chart = new ApexCharts(element, {
                ...options,
                theme: {
                    mode: 'dark',
                    ...(options.theme || {}),
                },
                chart: {
                    background: 'transparent',
                    foreColor: '#94a3b8',
                    ...(options.chart || {}),
                },
            });
            chart.render();
            element.dataset.rendered = 'true';
        } catch (error) {
            console.error('Unable to render chart', error);
        }
    });
};

document.addEventListener('DOMContentLoaded', renderCharts);

document.addEventListener('submit', (event) => {
    const submitter = event.submitter;
    if (!submitter || !submitter.dataset || submitter.dataset.loading !== 'true') return;

    submitter.disabled = true;
    submitter.classList.add('pointer-events-none', 'opacity-70');
    const originalText = submitter.innerHTML;
    submitter.innerHTML = '<span class="spinner" aria-hidden="true"></span><span>Memproses…</span>';
    window.setTimeout(() => {
        if (submitter.isConnected) submitter.innerHTML = originalText;
    }, 8000);
});

Alpine.start();
