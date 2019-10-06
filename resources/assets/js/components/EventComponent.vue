<template>
    <div class="mt-5">
        <div class="row">
            <div class="col col-5">
                <form @submit.prevent="addEvent()" class="mb-3">
                    <div class="form-group">
                        <label for="title">Event</label>
                        <input type="text" class="form-control" placeholder="Event title" id="title" v-model="event.name">
                    </div>
                    <div class="row">
                        <div class="col form-group">
                            <label for="start">From</label>
                            <input type="date" class="form-control" id="start" v-model="event.start">
                        </div>
                        <div class="col form-group">
                            <label for="end">To</label>
                            <input type="date" class="form-control" id="end" v-model="event.end">
                        </div>
                    </div>
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
                    <button class="btn btn-success btn-block" type="submit">Save</button>
                </form>
                <hr>
                <div v-if="errorFlag">
                    <div class="alert alert-danger" v-for="message in messages" :key="message.id">
                        {{ message }}
                    </div>
                </div>
                <div v-if="successFlag">
                    <div class="alert alert-success" v-for="message in messages" :key="message.id">
                        {{ message }}
                    </div>
                </div>
            </div>
            <div class="col">
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

    export default {
        components: {
            FullCalendar
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
                errorFlag: false,
                successFlag: false,
                messages: []
            }
        },
        methods: {
            addEvent() {
                this.messages = [];
                this.dates = [];
                
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
                        this.successFlag = false;
                        this.errorFlag = true;
                        this.messages = data.messages;
                    } else {
                        this.populateCalendar(data.data);
                        this.successFlag = true;
                        this.errorFlag = false;
                        this.messages = data.messages;
                    }    
                })
                .catch(err => console.log(err));
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

                this.dates = dates;
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