<template>
    <div class="slip-gaji-dashboard">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <i class="fa fa-dashboard"></i> 
                            Dashboard Slip Gaji
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h4><i class="fa fa-user"></i> Informasi Karyawan</h4>
                                <div class="form-group">
                                    <label>Nama Karyawan:</label>
                                    <p class="form-control-static">{{ employee.name }}</p>
                                </div>
                                <div class="form-group">
                                    <label>Jabatan:</label>
                                    <p class="form-control-static">{{ employee.position }}</p>
                                </div>
                                <div class="form-group">
                                    <label>ID Karyawan:</label>
                                    <p class="form-control-static">{{ employee.id }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h4><i class="fa fa-money"></i> Detail Gaji</h4>
                                <table class="table table-striped">
                                    <tr>
                                        <td><strong>Gaji Pokok:</strong></td>
                                        <td>Rp {{ formatCurrency(salary.basic) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tunjangan:</strong></td>
                                        <td>Rp {{ formatCurrency(salary.allowances) }}</td>
                                    </tr>
                                    <tr class="text-danger">
                                        <td><strong>Potongan:</strong></td>
                                        <td>Rp {{ formatCurrency(salary.deductions) }}</td>
                                    </tr>
                                    <tr class="success">
                                        <td><strong>Gaji Bersih:</strong></td>
                                        <td><strong>Rp {{ formatCurrency(netSalary) }}</strong></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="btn-group" role="group">
                                    <button @click="generateSlip" class="btn btn-success">
                                        <i class="fa fa-file-text"></i> Generate Slip Gaji
                                    </button>
                                    <button @click="downloadPDF" class="btn btn-primary">
                                        <i class="fa fa-download"></i> Download PDF
                                    </button>
                                    <button @click="sendEmail" class="btn btn-info">
                                        <i class="fa fa-envelope"></i> Kirim Email
                                    </button>
                                    <button @click="printSlip" class="btn btn-default">
                                        <i class="fa fa-print"></i> Print
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="panel panel-success">
                    <div class="panel-body text-center">
                        <h3><i class="fa fa-users"></i></h3>
                        <h4>{{ stats.totalEmployees }}</h4>
                        <p>Total Karyawan</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="panel panel-info">
                    <div class="panel-body text-center">
                        <h3><i class="fa fa-file-text"></i></h3>
                        <h4>{{ stats.slipsThisMonth }}</h4>
                        <p>Slip Bulan Ini</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="panel panel-warning">
                    <div class="panel-body text-center">
                        <h3><i class="fa fa-money"></i></h3>
                        <h4>Rp {{ formatCurrency(stats.totalPayroll) }}</h4>
                        <p>Total Payroll</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="panel panel-primary">
                    <div class="panel-body text-center">
                        <h3><i class="fa fa-calendar"></i></h3>
                        <h4>{{ getCurrentMonth() }}</h4>
                        <p>Periode Aktif</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'SlipGaji',
    data() {
        return {
            employee: {
                name: 'John Doe',
                position: 'Software Developer',
                id: 'EMP001'
            },
            salary: {
                basic: 5000000,
                allowances: 1500000,
                deductions: 750000
            },
            stats: {
                totalEmployees: 25,
                slipsThisMonth: 18,
                totalPayroll: 125000000
            }
        }
    },
    computed: {
        netSalary() {
            return this.salary.basic + this.salary.allowances - this.salary.deductions;
        }
    },
    methods: {
        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID').format(amount);
        },
        getCurrentMonth() {
            const months = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];
            return months[new Date().getMonth()];
        },
        generateSlip() {
            // Simulate API call
            this.$set(this, 'isLoading', true);
            setTimeout(() => {
                alert('Slip gaji berhasil di-generate!');
                this.$set(this, 'isLoading', false);
            }, 1000);
        },
        downloadPDF() {
            alert('Downloading PDF slip gaji...');
            // Logic untuk download PDF
        },
        sendEmail() {
            alert('Mengirim slip gaji via email...');
            // Logic untuk send email
        },
        printSlip() {
            window.print();
        }
    },
    mounted() {
        console.log('SlipGaji Vue component mounted successfully!');
    }
}
</script>

<style scoped>
.slip-gaji-dashboard {
    margin-top: 20px;
}

.panel-primary > .panel-heading {
    background-color: #337ab7;
    border-color: #337ab7;
}

.btn-group .btn {
    margin-right: 5px;
}

.panel-body h3 {
    margin-top: 0;
    color: #333;
}

.table-striped tr.success td {
    background-color: #dff0d8;
    font-weight: bold;
}

.form-control-static {
    font-weight: bold;
    color: #333;
}

@media (max-width: 768px) {
    .btn-group .btn {
        width: 100%;
        margin-bottom: 5px;
    }
}
</style>
