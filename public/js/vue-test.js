// Test Vue.js Component - Simple Version
Vue.component('test-component', {
    template: `
    <div class="alert alert-success" style="margin-top: 20px;">
        <h3><i class="fa fa-check"></i> Vue.js Berhasil Dimuat!</h3>
        <p>{{ message }}</p>
        <p><strong>Gaji:</strong> {{ formatCurrency(salary) }}</p>
    </div>
    `,
    data() {
        return {
            message: 'Komponen Vue.js berfungsi dengan baik',
            salary: 9700000
        }
    },
    methods: {
        formatCurrency(amount) {
            return 'Rp ' + amount.toLocaleString('id-ID');
        }
    }
});

// Initialize Vue application
var app = new Vue({
    el: '#app',
    data: {
        message: 'Vue.js Application Ready!'
    }
});
