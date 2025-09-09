// Vue.js Application for Slip Gaji
Vue.component('slip-gaji', {
    template: `
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <i class="fa fa-user"></i> Informasi Karyawan
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>NIK:</strong> {{ employee.nik }}</p>
                                <p><strong>Nama:</strong> {{ employee.name }}</p>
                                <p><strong>Jabatan:</strong> {{ employee.position }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Departemen:</strong> {{ employee.department }}</p>
                                <p><strong>Tanggal Bergabung:</strong> {{ employee.joinDate }}</p>
                                <p><strong>Status:</strong> {{ employee.status }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="panel panel-success">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <i class="fa fa-calculator"></i> Gaji Bersih
                        </h3>
                    </div>
                    <div class="panel-body text-center">
                        <h2 class="text-success">{{ formatCurrency(netSalary) }}</h2>
                        <p class="text-muted">Periode: {{ currentPeriod }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <i class="fa fa-plus-circle"></i> Pendapatan
                        </h3>
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped">
                            <tbody>
                                <tr v-for="item in earnings" :key="item.name">
                                    <td>{{ item.name }}</td>
                                    <td class="text-right">{{ formatCurrency(item.amount) }}</td>
                                </tr>
                                <tr class="info">
                                    <td><strong>Total Pendapatan</strong></td>
                                    <td class="text-right"><strong>{{ formatCurrency(totalEarnings) }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="panel panel-warning">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <i class="fa fa-minus-circle"></i> Potongan
                        </h3>
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped">
                            <tbody>
                                <tr v-for="item in deductions" :key="item.name">
                                    <td>{{ item.name }}</td>
                                    <td class="text-right">{{ formatCurrency(item.amount) }}</td>
                                </tr>
                                <tr class="warning">
                                    <td><strong>Total Potongan</strong></td>
                                    <td class="text-right"><strong>{{ formatCurrency(totalDeductions) }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12 text-center">
                <button class="btn btn-primary btn-lg" @click="downloadPDF">
                    <i class="fa fa-download"></i> Download Slip Gaji PDF
                </button>
                <button class="btn btn-success btn-lg" @click="sendEmail">
                    <i class="fa fa-envelope"></i> Kirim ke Email
                </button>
                <button class="btn btn-info btn-lg" @click="printSlip">
                    <i class="fa fa-print"></i> Print Slip
                </button>
            </div>
        </div>
    </div>
    `,
    data() {
        return {
            employee: {
                nik: '12345678',
                name: 'John Doe',
                position: 'Senior Developer',
                department: 'IT Development',
                joinDate: '01 January 2020',
                status: 'Aktif'
            },
            earnings: [
                { name: 'Gaji Pokok', amount: 8000000 },
                { name: 'Tunjangan Transport', amount: 500000 },
                { name: 'Tunjangan Makan', amount: 600000 },
                { name: 'Tunjangan Kesehatan', amount: 300000 },
                { name: 'Bonus Kinerja', amount: 1000000 }
            ],
            deductions: [
                { name: 'PPh 21', amount: 450000 },
                { name: 'BPJS Kesehatan', amount: 120000 },
                { name: 'BPJS Ketenagakerjaan', amount: 80000 },
                { name: 'Potongan Lain', amount: 50000 }
            ],
            currentPeriod: 'November 2024'
        }
    },
    computed: {
        totalEarnings() {
            return this.earnings.reduce((total, item) => total + item.amount, 0);
        },
        totalDeductions() {
            return this.deductions.reduce((total, item) => total + item.amount, 0);
        },
        netSalary() {
            return this.totalEarnings - this.totalDeductions;
        }
    },
    methods: {
        formatCurrency(amount) {
            return 'Rp ' + amount.toLocaleString('id-ID');
        },
        downloadPDF() {
            alert('Fitur download PDF akan segera dikembangkan!');
        },
        sendEmail() {
            alert('Fitur kirim email akan segera dikembangkan!');
        },
        printSlip() {
            window.print();
        }
    }
});

// Initialize Vue application
var app = new Vue({
    el: '#app',
    data: {
        message: 'Slip Gaji Application Ready!'
    }
});
