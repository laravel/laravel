@extends('layouts.app')

@section('content')
<div class="container">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fa fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="fa fa-calendar"></i> Pilih Rentang Tanggal Slip Gaji
                    </h3>
                </div>
                <div class="panel-body">
                    <div id="date-picker-app">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Tanggal Mulai</label>
                                    <input type="date" class="form-control" id="startDate" v-model="startDate" @change="onStartDateChange">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Tanggal Akhir</label>
                                    <input type="date" class="form-control" id="endDate" v-model="endDate" @change="onEndDateChange" :min="startDate">
                                </div>
                            </div>
                        </div>
                        
                        <div v-show="startDate && endDate" class="alert alert-info">
                            <div class="text-center">
                                <h4><i class="fa fa-calendar-check-o"></i> Rentang yang dipilih:</h4>
                                <p class="lead">
                                    <strong id="dateRange"></strong>
                                </p>
                                <p class="text-muted" id="dayCount"></p>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <button type="button" class="btn btn-default btn-block" @click="resetDates">
                                    <i class="fa fa-refresh"></i> Reset
                                </button>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn btn-primary btn-block" :disabled="!startDate || !endDate" @click="confirmSelection">
                                    <i class="fa fa-check"></i> Konfirmasi Pilihan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Simple date picker using HTML5 date inputs
new Vue({
    el: '#date-picker-app',
    data: {
        startDate: '',
        endDate: ''
    },
    methods: {
        onStartDateChange: function() {
            if (this.endDate && this.startDate > this.endDate) {
                this.endDate = '';
            }
            this.updateDisplay();
        },
        onEndDateChange: function() {
            this.updateDisplay();
        },
        formatDisplayDate: function(dateString) {
            if (!dateString) return '';
            var date = new Date(dateString);
            var months = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];
            return date.getDate() + ' ' + months[date.getMonth()] + ' ' + date.getFullYear();
        },
        getDaysDifference: function() {
            if (!this.startDate || !this.endDate) return 0;
            var start = new Date(this.startDate);
            var end = new Date(this.endDate);
            return Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
        },
        updateDisplay: function() {
            var dateRangeEl = document.getElementById('dateRange');
            var dayCountEl = document.getElementById('dayCount');
            
            if (this.startDate && this.endDate && dateRangeEl && dayCountEl) {
                dateRangeEl.textContent = this.formatDisplayDate(this.startDate) + ' - ' + this.formatDisplayDate(this.endDate);
                dayCountEl.textContent = this.getDaysDifference() + ' hari';
            }
        },
        resetDates: function() {
            this.startDate = '';
            this.endDate = '';
            this.updateDisplay();
        },
        confirmSelection: function() {
            if (this.startDate && this.endDate) {
                // Convert to ISO format for storage
                var startDateObj = new Date(this.startDate);
                var endDateObj = new Date(this.endDate);
                
                localStorage.setItem('selectedStartDate', startDateObj.toISOString());
                localStorage.setItem('selectedEndDate', endDateObj.toISOString());
                
                // Redirect to home
                window.location.href = '/home';
            }
        }
    },
    mounted: function() {
        // Set default to current month
        var today = new Date();
        var firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        var lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
        
        this.startDate = firstDay.toISOString().split('T')[0];
        this.endDate = lastDay.toISOString().split('T')[0];
        
        // Update display after setting defaults
        this.$nextTick(function() {
            this.updateDisplay();
        });
    }
});
</script>

<style>
.form-control {
    display: block;
    width: 100%;
    height: 34px;
    padding: 6px 12px;
    font-size: 14px;
    line-height: 1.42857143;
    color: #555;
    background-color: #fff;
    background-image: none;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
}

.form-control:focus {
    border-color: #66afe9;
    outline: 0;
    box-shadow: inset 0 1px 1px rgba(0,0,0,.075),0 0 8px rgba(102,175,233,.6);
}

.alert-info {
    background-color: #d9edf7;
    border-color: #bce8f1;
    color: #31708f;
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
}

.alert-info .fa {
    margin-right: 5px;
}

.btn {
    display: inline-block;
    padding: 6px 12px;
    margin-bottom: 0;
    font-size: 14px;
    font-weight: normal;
    line-height: 1.42857143;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    cursor: pointer;
    border: 1px solid transparent;
    border-radius: 4px;
    text-decoration: none;
}

.btn:disabled {
    cursor: not-allowed;
    opacity: 0.65;
}

.btn-primary {
    color: #fff;
    background-color: #337ab7;
    border-color: #2e6da4;
}

.btn-primary:hover {
    color: #fff;
    background-color: #286090;
    border-color: #204d74;
}

.btn-default {
    color: #333;
    background-color: #fff;
    border-color: #ccc;
}

.btn-default:hover {
    color: #333;
    background-color: #e6e6e6;
    border-color: #adadad;
}

.btn-block {
    display: block;
    width: 100%;
}

.text-center {
    text-align: center;
}

.text-muted {
    color: #777;
}

.lead {
    margin-bottom: 20px;
    font-size: 16px;
    font-weight: 300;
    line-height: 1.4;
}

.form-group {
    margin-bottom: 15px;
}

.control-label {
    margin-bottom: 5px;
    font-weight: bold;
}
</style>
@endsection
