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
                    <!-- Vue.js Date Range Picker Component -->
                    <date-range-picker></date-range-picker>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Vue.js Date Range Picker Component
Vue.component('date-range-picker', {
    template: `
    <div>
        <div class="row">
            <div class="col-md-6">
                <!-- Start Date Picker -->
                <div class="form-group">
                    <label class="control-label">Tanggal Mulai</label>
                    <div class="input-group">
                        <input 
                            type="text" 
                            class="form-control" 
                            :value="formatDate(startDate)" 
                            @click="toggleStartCalendar"
                            placeholder="Pilih tanggal mulai"
                            readonly
                        >
                        <span class="input-group-addon" @click="toggleStartCalendar">
                            <i class="fa fa-calendar"></i>
                        </span>
                    </div>
                    
                    <!-- Start Calendar Dropdown -->
                    <div v-show="showStartCalendar" class="calendar-dropdown">
                        <div class="calendar-header">
                            <button type="button" class="btn btn-xs btn-default" @click="navigateMonth(-1, true)">
                                <i class="fa fa-chevron-left"></i>
                            </button>
                            <span class="calendar-title">
                                @{{ monthNames[startCurrentMonth.getMonth()] }} @{{ startCurrentMonth.getFullYear() }}
                            </span>
                            <button type="button" class="btn btn-xs btn-default" @click="navigateMonth(1, true)">
                                <i class="fa fa-chevron-right"></i>
                            </button>
                        </div>
                        
                        <div class="calendar-grid">
                            <div class="calendar-days-header">
                                <div v-for="day in dayNames" class="calendar-day-name">@{{ day }}</div>
                            </div>
                            <div class="calendar-days">
                                <div 
                                    v-for="(date, index) in getStartCalendarDays()" 
                                    :key="index"
                                    :class="getDateClass(date, true)"
                                    @click="selectStartDate(date)"
                                >
                                    @{{ date ? date.getDate() : '' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <!-- End Date Picker -->
                <div class="form-group">
                    <label class="control-label">Tanggal Akhir</label>
                    <div class="input-group">
                        <input 
                            type="text" 
                            class="form-control" 
                            :value="formatDate(endDate)" 
                            @click="toggleEndCalendar"
                            placeholder="Pilih tanggal akhir"
                            readonly
                        >
                        <span class="input-group-addon" @click="toggleEndCalendar">
                            <i class="fa fa-calendar"></i>
                        </span>
                    </div>
                    
                    <!-- End Calendar Dropdown -->
                    <div v-show="showEndCalendar" class="calendar-dropdown">
                        <div class="calendar-header">
                            <button type="button" class="btn btn-xs btn-default" @click="navigateMonth(-1, false)">
                                <i class="fa fa-chevron-left"></i>
                            </button>
                            <span class="calendar-title">
                                @{{ monthNames[endCurrentMonth.getMonth()] }} @{{ endCurrentMonth.getFullYear() }}
                            </span>
                            <button type="button" class="btn btn-xs btn-default" @click="navigateMonth(1, false)">
                                <i class="fa fa-chevron-right"></i>
                            </button>
                        </div>
                        
                        <div class="calendar-grid">
                            <div class="calendar-days-header">
                                <div v-for="day in dayNames" class="calendar-day-name">@{{ day }}</div>
                            </div>
                            <div class="calendar-days">
                                <div 
                                    v-for="(date, index) in getEndCalendarDays()" 
                                    :key="index"
                                    :class="getDateClass(date, false)"
                                    @click="selectEndDate(date)"
                                >
                                    @{{ date ? date.getDate() : '' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Selected Range Summary -->
        <div v-if="startDate && endDate" class="alert alert-info">
            <div class="text-center">
                <h4><i class="fa fa-calendar-check-o"></i> Rentang yang dipilih:</h4>
                <p class="lead">
                    <strong>@{{ formatDate(startDate) }} - @{{ formatDate(endDate) }}</strong>
                </p>
                <p class="text-muted">
                    @{{ getDaysDifference() }} hari
                </p>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="row">
            <div class="col-md-6">
                <button 
                    type="button" 
                    class="btn btn-default btn-block"
                    @click="resetDates"
                >
                    <i class="fa fa-refresh"></i> Reset
                </button>
            </div>
            <div class="col-md-6">
                <button 
                    type="button" 
                    class="btn btn-primary btn-block"
                    :disabled="!startDate || !endDate"
                    @click="confirmSelection"
                >
                    <i class="fa fa-check"></i> Konfirmasi Pilihan
                </button>
            </div>
        </div>
    </div>
    `,
    data() {
        return {
            startDate: null,
            endDate: null,
            showStartCalendar: false,
            showEndCalendar: false,
            startCurrentMonth: new Date(),
            endCurrentMonth: new Date(),
            monthNames: [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ],
            dayNames: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab']
        }
    },
    methods: {
        toggleStartCalendar() {
            this.showStartCalendar = !this.showStartCalendar;
            this.showEndCalendar = false;
        },
        toggleEndCalendar() {
            this.showEndCalendar = !this.showEndCalendar;
            this.showStartCalendar = false;
        },
        navigateMonth(direction, isStart) {
            if (isStart) {
                let newMonth = new Date(this.startCurrentMonth);
                newMonth.setMonth(newMonth.getMonth() + direction);
                this.startCurrentMonth = newMonth;
            } else {
                let newMonth = new Date(this.endCurrentMonth);
                newMonth.setMonth(newMonth.getMonth() + direction);
                this.endCurrentMonth = newMonth;
            }
        },
        getDaysInMonth(date) {
            let year = date.getFullYear();
            let month = date.getMonth();
            let firstDay = new Date(year, month, 1);
            let lastDay = new Date(year, month + 1, 0);
            let firstDayWeekday = firstDay.getDay();
            let daysInMonth = lastDay.getDate();

            let days = [];
            
            // Add empty cells for days before month starts
            for (let i = 0; i < firstDayWeekday; i++) {
                days.push(null);
            }
            
            // Add all days of the month
            for (let day = 1; day <= daysInMonth; day++) {
                days.push(new Date(year, month, day));
            }
            
            return days;
        },
        getStartCalendarDays() {
            return this.getDaysInMonth(this.startCurrentMonth);
        },
        getEndCalendarDays() {
            return this.getDaysInMonth(this.endCurrentMonth);
        },
        formatDate(date) {
            if (!date) return '';
            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short', 
                year: 'numeric'
            });
        },
        isSameDay(date1, date2) {
            if (!date1 || !date2) return false;
            return date1.toDateString() === date2.toDateString();
        },
        isInRange(date) {
            if (!this.startDate || !this.endDate || !date) return false;
            return date >= this.startDate && date <= this.endDate;
        },
        getDateClass(date, isStart) {
            if (!date) return 'calendar-day calendar-day-empty';
            
            let classes = ['calendar-day'];
            
            let isSelected = (isStart && this.isSameDay(date, this.startDate)) || 
                           (!isStart && this.isSameDay(date, this.endDate));
            let inRange = this.isInRange(date);
            let isToday = this.isSameDay(date, new Date());
            let isDisabled = !isStart && this.startDate && date < this.startDate;
            
            if (isDisabled) {
                classes.push('calendar-day-disabled');
            } else if (isSelected) {
                classes.push(isStart ? 'calendar-day-start' : 'calendar-day-end');
            } else if (inRange) {
                classes.push('calendar-day-range');
            } else if (isToday) {
                classes.push('calendar-day-today');
            } else {
                classes.push('calendar-day-available');
            }
            
            return classes.join(' ');
        },
        selectStartDate(date) {
            if (!date) return;
            this.startDate = date;
            if (this.endDate && date > this.endDate) {
                this.endDate = null;
            }
            this.showStartCalendar = false;
        },
        selectEndDate(date) {
            if (!date) return;
            if (this.startDate && date < this.startDate) {
                return; // Don't allow end date before start date
            }
            this.endDate = date;
            this.showEndCalendar = false;
        },
        getDaysDifference() {
            if (!this.startDate || !this.endDate) return 0;
            return Math.ceil((this.endDate - this.startDate) / (1000 * 60 * 60 * 24)) + 1;
        },
        resetDates() {
            this.startDate = null;
            this.endDate = null;
            this.showStartCalendar = false;
            this.showEndCalendar = false;
        },
        confirmSelection() {
            if (this.startDate && this.endDate) {
                // Simpan tanggal di localStorage untuk digunakan di halaman home
                localStorage.setItem('selectedStartDate', this.startDate.toISOString());
                localStorage.setItem('selectedEndDate', this.endDate.toISOString());
                
                // Redirect ke halaman home
                window.location.href = '/home';
            }
        }
    },
    mounted() {
        // Close calendars when clicking outside
        document.addEventListener('click', (e) => {
            if (!this.$el.contains(e.target)) {
                this.showStartCalendar = false;
                this.showEndCalendar = false;
            }
        });
    }
});

// Initialize Vue app if not already initialized
if (typeof app === 'undefined') {
    var app = new Vue({
        el: '#app'
    });
}
</script>

<style>
.calendar-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    z-index: 1000;
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    margin-top: 2px;
    padding: 15px;
}

.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding: 0 5px;
}

.calendar-title {
    font-weight: bold;
    color: #333;
}

.calendar-days-header {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 2px;
    margin-bottom: 5px;
}

.calendar-day-name {
    text-align: center;
    font-size: 12px;
    font-weight: bold;
    color: #666;
    padding: 8px 4px;
}

.calendar-days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 2px;
}

.calendar-day {
    text-align: center;
    padding: 8px 4px;
    cursor: pointer;
    border-radius: 3px;
    font-size: 14px;
    transition: all 0.2s ease;
    min-height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.calendar-day-empty {
    cursor: default;
}

.calendar-day-available:hover {
    background-color: #f5f5f5;
}

.calendar-day-start {
    background-color: #337ab7;
    color: white;
    font-weight: bold;
}

.calendar-day-end {
    background-color: #5cb85c;
    color: white;
    font-weight: bold;
}

.calendar-day-range {
    background-color: #d9edf7;
    color: #31708f;
}

.calendar-day-today {
    background-color: #f0ad4e;
    color: white;
    font-weight: bold;
}

.calendar-day-disabled {
    color: #ccc;
    cursor: not-allowed;
}

.calendar-day-disabled:hover {
    background-color: transparent;
}

.form-group {
    position: relative;
}
</style>
@endsection