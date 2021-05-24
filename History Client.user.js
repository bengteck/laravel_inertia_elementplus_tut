// ==UserScript==
// @name         History Client
// @namespace    http://tampermonkey.net/
// @version      0.1
// @description  try to take over the world!
// @author       You
// @match        https://www.vschooltrend.com/
// @match        https://www.vschooltrend.com/web
// @match        https://www.vschooltrend.com/dashboard
// @match        https://www.vschooltrend.com/myProgress
// @icon         https://www.google.com/s2/favicons?domain=vschooltrend.com
// @grant        none
// ==/UserScript==

(function() {
    'use strict';

    const action_channel = new BroadcastChannel('vschool-data');
    action_channel.onmessage = (event) => {
        action(event.data)
    }

    setTimeout( () => { startonload() },1000)

    const startonload = () => {
        if( window.location.href == "https://www.vschooltrend.com/" ) {
            action_channel.postMessage({ response: 'go_main' })
            
        } else if( window.location.href == "https://www.vschooltrend.com/dashboard" ) {
            action_channel.postMessage({ response: 'login' })

        } else if( window.location.href == "https://www.vschooltrend.com/myProgress" ) {

            if( document.querySelectorAll('table.progress').length > 0 ) {
                console.log('get_history loaded',document.querySelectorAll('table.progress tbody tr').length)
                let histories = []
                C.forEach( (tr) => {
                    const td = tr.querySelectorAll('td')
                    if( td.length > 0 ) {
                        histories.push({
                            level:    td[1].textContent,
                            date:     td[2].textContent,
                            subject:  td[3].textContent,
                            type:     td[4].textContent,
                            unit:     td[5].textContent,
                            section:  td[6].textContent,
                            duration: td[7].textContent,
                            score:    td[8].textContent,
                            link:     td[9].querySelector('a').href,
                        })
                    }
                })
                const response = {
                    response: 'get_history',
                    data : document.querySelector('form#formSearch #level').selectedIndex,
                    results: histories
                }
                console.log(response)
                action_channel.postMessage(response)
            } else if( document.querySelector('form#formSearch #sub_id').value != "" ) {
                action_channel.postMessage({
                    response: 'set_subject',
                    data: document.querySelector('form#formSearch #sub_id').value,
                })
            } else if( document.querySelector('form#formSearch #kid_id').value != "" ) {
                action_channel.postMessage({
                    response: 'set_kid',
                    data: document.querySelector('form#formSearch #kid_id').value,
                })

            } else {
                action_channel.postMessage({ response: 'go_myprogress' })
            }
        }
    }

    const action = (command) => {
        const kids = []
        const subjects = []
        const levels = []
        switch (command.request) {
            case 'login':
                document.querySelector("[name='username']").value = command.param.user
                document.querySelector("[name='password']").value = command.param.pass
                document.querySelector("[type='submit']").click()
                break
            case 'go_myprogress':
                window.location = "https://www.vschooltrend.com/myProgress"
                break
            case 'go_main':
                window.location = "https://www.vschooltrend.com"
                break
            case 'get_kids':
                document.querySelectorAll('#bgSubject')[0]
                    .querySelectorAll('li')
                    .forEach( (li) => {
                    kids.push({
                        kid_id: li.querySelector('a').dataset.id,
                        name: li.querySelector('.myLearningSubName').textContent,
                    })
                })
                action_channel.postMessage({ response: 'get_kids', results: kids })
                break
            case 'set_kid':
                document.querySelector('form#formSearch #kid_id').value = command.data //128202
                document.querySelector('form#formSearch #sub_id').value = "" //87
                document.querySelector('form#formSearch').submit()
                break
            case 'get_subjects':
                document.querySelectorAll('ul.nav.nav-tabs')[1].querySelectorAll('li').forEach((li) => {
                    subjects.push({
                        sub_id: li.querySelector('a').dataset.id,
                        label: li.querySelector('p.myLearningSubName').textContent,
                    })
                })
                action_channel.postMessage({ response: 'get_subjects', results: subjects })
                break
            case 'set_subject':
                document.querySelector('form#formSearch #sub_id').value = command.data //87
                document.querySelector('form#formSearch').submit()
                break
            case 'get_levels':
                document.querySelectorAll("select[name='level'] option")
                    .forEach( (option) => {
                    if(! option.value ) return;

                    levels.push({
                        level: (levels.length + 1),
                        label: option.innerHTML,
                    })
                })
                action_channel.postMessage({ response: 'get_levels', results: levels })
                break
            case 'get_history':
                document.querySelector('form#formSearch select#level').selectedIndex = command.param.level //'5|87'
                myFunctionx()
                document.querySelector('form#formSearch #datepicker').value = command.param.dateFrom //'5|87'
                document.querySelector('form#formSearch #datepicker2').value = command.param.dateTo //'5|87'
                setTimeout(()=>{ document.querySelector("form#formSearch input[type='submit']").click() },1500)
        }
    }
})();