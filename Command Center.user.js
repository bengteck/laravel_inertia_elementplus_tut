// ==UserScript==
// @name         Command Center
// @namespace    http://tampermonkey.net/
// @version      0.1
// @description  try to take over the world!
// @author       You
// @match        https://www.vschooltrend.com/?control
// @icon         https://www.google.com/s2/favicons?domain=vschooltrend.com
// @grant        none
// ==/UserScript==

(function() {
    'use strict';

    const head = document.getElementsByTagName("head")[0]
    const body = document.getElementsByTagName("body")[0]
    const accounts = document.createElement('div')
    body.appendChild(accounts)
    accounts.innerHTML = `
<div id="control_panel">
 <span>parentaccs:</span>
 <textarea id="parent_accounts" height="100">
chanbengteck,chanbengteck
hiichiikok,frieda73
khookhiamping,123Abc
</textarea>
 <span>dates :</span>
 <textarea id="history_dates" height="10">
2021-05-15
 </textarea>
 <span>log :</span>
 <pre id="output_log">Log here</pre>
 <div></div>
 <button id="start_extract">start</button>
</div>
`
    const style = document.createElement('style')
    head.appendChild(style);
    style.textContent = `
#control_panel {
    display: grid;
    grid-template-columns: 100px 1fr;
    position: absolute;
    top: 11px;
    background: white;
    width: 97vw;
    left: 11px;
    height: 50vh;
    padding: 15px;
    z-index: +10;
}
#control_panel * {
    color:black;
    background-color:white;
}
`
    const getParentAccount = () => {
        return document.querySelector('#parent_accounts').value
            .trim().split('\n').map((acc) => {
            const x = acc.split(',')
            return {
                user: x[0],
                pass: x[1],
            }
        })
    }
    const getDates = () => {
        return document.querySelector('#history_dates').value
            .trim().split('\n').map((dates) => {
                const x = dates.split(',')
                if(x.length == 1){
                    return {
                        from: x[0],
                        to: x[0],
                    }
                }
                return {
                    from: x[0],
                    to: x[1],
                }
            });
    }

    const goPage = (command, sec = 10) => {
        return new Promise((resolve, reject) => {
            const channel = new BroadcastChannel('vschool-data');
            // dont wait too long
            const timer = setTimeout(() => {
                channel.close()
                reject('page load timeout ' + command.request)
            }, sec * 1000)
            // return success on notify
            channel.onmessage = (event) => {
                if( event.data.response != command.request ) return;
                if( command.data && command.data != event.data.data ) return;

                clearTimeout(timer)
                channel.close()
                resolve(event.data.results)
            }
            channel.postMessage(command)
        })
    }


    const start = async () => {
        try {
            const parents = getParentAccount()
            const dates = getDates()
            for (let parentIdx = 0; parentIdx < parents.length; parentIdx++) {
                const parent = parents[parentIdx];
                await goPage({ request: 'login', param: parent})
                await goPage({ request: 'go_myprogress'})
                const kids = await goPage({ request: 'get_kids'})

                for (let kidIdx = 0; kidIdx < kids.length; kidIdx++) {
                    const kid = kids[kidIdx];
                    await goPage({ request: 'set_kid', data: kid.kid_id })
                    const subjects = await goPage({ request: 'get_subjects' })
                    for (let subjectIdx = 0; subjectIdx < subjects.length; subjectIdx++) {
                        const subject = subjects[subjectIdx];
                        console.log('set_subject ' + subject.sub_id)
                        await goPage({ request: 'set_subject', data: subject.sub_id })
                        const levels = await goPage({ request: 'get_levels'})

                        for (let levelIdx = 0; levelIdx < levels.length; levelIdx++) {
                            const level = levels[levelIdx];

                            for (let dateIdx = 0; dateIdx < dates.length; dateIdx++) {
                                const date = dates[dateIdx];
                                const results = await goPage({
                                    request: 'get_history',
                                    data: level.level,
                                    param: {
                                        level: level.level,
                                        dateFrom: date.from,
                                        dateTo: date.to,
                                    }
                                },20)
                                const submitdata = results.map( (x) => {
                                    return {
                                        parent: parent.user,
                                        kid_id: kid.kid_id,
                                        kid_name: kid.name,
                                        level:x.level,
                                        date: x.date,
                                        subject_id: subject.sub_id,
                                        subject: subject.label,
                                        type: x.type,
                                        unit: x.unit,
                                        section: x.section,
                                        duration: x.duration,
                                        score: x.score,
                                        link: x.link,
                                    }
                                })
                                submitdata.forEach( (x) => log(JSON.stringify(x)) )
                                if( submitdata.length > 0 ) {
                                    fetch('http://localhost:8000/api/extracted', {
                                        method: 'POST',
                                        body: JSON.stringify(submitdata),
                                        headers: {
                                            "Content-type": "application/json; charset=UTF-8"
                                        }
                                    }).then(function(response) {
                                        return response.json();
                                    }).then(function(data) {
                                        console.log(data)
                                    }).catch((err) => {
                                        console.error(err)
                                    });
                                }
                            }
                        }
                    }
                }
                await goPage({ request: 'go_main' })
            }
        } catch (error) {
            console.error(error)
        }
    }

    document.querySelector('#start_extract').onclick = () => {
        start()
        return false
    }

    const log = (data) => {
        document.querySelector('#output_log').textContent += '\n' + data
    }

})()
