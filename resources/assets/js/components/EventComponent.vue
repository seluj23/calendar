<template>
    <div class="mt-5">
        <div class="row">
            <div class="col-xs-12 col col-md-5">
                
                <!-- Form -->
                <form @submit.prevent="addEvent()" class="mb-3">

                    <!-- Input text Title -->
                    <div class="form-group">
                        <label for="title">Event</label>
                        <input type="text" class="form-control" placeholder="Enter Name" id="title" v-model="event.name">
                    </div>
                    <div class="row">

                        <!-- Datepicker From -->
                        <div class="col form-group">
                            <label for="start">From</label>
                            <date-picker id="start" v-model="event.start" :lang="datepicker.lang" :width="datepicker.width" :value-type="datepicker.valueType"></date-picker>
                        </div>

                        <!-- Datepicker To -->
                        <div class="col form-group">
                            <label for="end">To</label>
                            <date-picker id="end" v-model="event.end" :lang="datepicker.lang" :width="datepicker.width" :value-type="datepicker.valueType"></date-picker>
                        </div>
                    </div>

                    <!-- Checkbox Days -->
                    <label for="check">Filter days</label>
                    <div class="mb-3" id="check">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="Sun" value="option1" v-model="event.sunday">
                            <label class="form-check-label" for="Sun">Sun</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="Mon" value="option1" v-model="event.monday">
                            <label class="form-check-label" for="Mon">Mon</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="Tue" value="option1" v-model="event.tuesday">
                            <label class="form-check-label" for="Tue">Tue</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="Wed" value="option1" v-model="event.wednesday">
                            <label class="form-check-label" for="Wed">Wed</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="Thu" value="option1" v-model="event.thursday">
                            <label class="form-check-label" for="Thu">Thu</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="Fri" value="option1" v-model="event.friday">
                            <label class="form-check-label" for="Fri">Fri</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="Sat" value="option1" v-model="event.saturday">
                            <label class="form-check-label" for="Sat">Sat</label>
                        </div>
                    </div>

                    <!-- Submit Form -->
                    <button class="btn btn-success btn-block" type="submit">Save</button>
                </form>
                <hr>

                <!-- Alert Error -->
                <div v-if="errorFlag">
                    <div class="alert alert-danger" v-for="message in messages" :key="message.id">
                        {{ message }}
                    </div>
                </div>

                <!-- Alert Success -->
                <div v-if="successFlag">
                    <div class="alert alert-success" v-for="message in messages" :key="message.id">
                        {{ message }}
                    </div>
                </div>

                <!-- Alert Warning -->
                <div v-if="warningFlag">
                    <div class="alert alert-warning" v-for="message in messages" :key="message.id">
                        {{ message }}
                    </div>
                </div>
            </div>

            <!-- Calendar -->
            <div class=" col-xs-12 col-md-7">
                <full-calendar 
                    defaultView="dayGridMonth"
                    :plugins="calendarPlugins"
                    :events="dates"
                />
            </div>
        </div>
    </div>
</template>

<script>
    import FullCalendar from '@fullcalendar/vue'
    import dayGridPlugin from '@fullcalendar/daygrid'
    import { diffDays, addDays } from '@fullcalendar/core';
    import DatePicker from 'vue2-datepicker'

    export default {
        components: {
            FullCalendar,
            DatePicker
        },
        data() {
            return {
                calendarPlugins: [ dayGridPlugin ],
                dates: [],
                event: {
                    name: '',
                    start: '',
                    end: '',
                    sunday: '',
                    monday: '',
                    tuesday: '',
                    wednesday: '',
                    thursday: '',
                    friday: '',
                    saturday: '',
                },
                datepicker: {
                    lang: {
                        days: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                        months: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        pickers: ['next 7 days', 'next 30 days', 'previous 7 days', 'previous 30 days'],
                        placeholder: {
                            date: 'Select Date',
                            dateRange: 'Select Date Range'
                        }
                    },
                    width: '100%',
                    valueType: 'format',
                },
                errorFlag: false,
                successFlag: false,
                warningFlag: false,
                messages: []
            }
        },
        methods: {
            addEvent() {
                this.dates = [];
                let vm = this.event;
                let start = new Date(vm.start);
                let end = new Date(vm.end);
                if (vm.name && vm.start && vm.end && start <= end && (vm.sunday || vm.monday || vm.tuesday || vm.wednesday || vm.thursday || vm.friday || vm.saturday)) {
                    fetch('api/event', {
                        method: 'post',
                        body: JSON.stringify(this.event),
                        headers: {
                            'content-type': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status == 'error') {
                            this.errorFlag = true;
                            this.successFlag = false;
                            this.warningFlag = false,
                            this.messages = data.messages;
                        } else {
                            this.errorFlag = false;
                            this.successFlag = true;
                            this.warningFlag = false,
                            this.messages = data.messages;
                            this.populateCalendar(data.data);
                        }    
                    })
                    .catch(err => console.log(err));
                } else {
                    let errors = [];
                    if (!this.event.name) {
                        errors.push('Event name is required.');
                    }
                    if (!this.event.start) {
                        errors.push('Date from is required.');
                    }
                    if (!this.event.end) {
                        errors.push('Date to is required.');
                    }
                    if (!(vm.sunday || vm.monday || vm.tuesday || vm.wednesday || vm.thursday || vm.friday || vm.saturday )) {
                        errors.push('Atleast one filter day is checked.');
                    }
                    if (!(start <= end) && start != 'Invalid Date' && end != 'Invalid Date') {
                        errors.push('Date to is earlier than date from.');
                    }

                    this.errorFlag = true;
                    this.successFlag = false;
                    this.warningFlag = false,
                    this.messages = errors;
                }
            },
            populateCalendar(data) {
                let dates = [];
                let week = [data.sunday, data.monday, data.tuesday, data.wednesday, data.thursday, data.friday, data.saturday]
                let start = new Date(data.start);
                let end = new Date(data.end);
                let period = diffDays(start, end);
                let day = start.getDay();
                for (let index = 0; index <= period; index++) {
                    let step = (day + index) % 7;
                    if (week[step]) {
                        dates.push({
                            title: data.title,
                            start: addDays(start, index)
                        });
                    }
                }

                if (dates.length > 0) {
                    this.dates = dates;
                } else {
                    this.errorFlag = false;
                    this.successFlag = false;
                    this.warningFlag = true;
                    this.messages = ['No date affected.'];
                }       
            }
        }
    }
</script>

<style lang='scss'>
    @import '~@fullcalendar/core/main.css';
    @import '~@fullcalendar/daygrid/main.css';

    .fc-time {
        display : none;
    }

    .fc-event, .fc-event-dot {
        background-color: lightgreen;
        border: none;
    }
</style>