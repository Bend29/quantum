import React from 'react';

export class Log extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            id: null,
            phone: null,
            text: '',
            data: [],
        };
        this.eventSource = new EventSource("events");
    }

    componentDidMount() {
        this.eventSource.onmessage = e =>
            //this.updateFromServer(JSON.parse(e.data));
            this.updateFromServer(e.data);
    }

    updateFromServer(line) {
        this.setState((prevState) => {
            let data = prevState.data;
            let id = prevState.id;
            let text = prevState.text;
            let parsed = JSON.parse(line);

            if (parsed.id !== id) {
                text = '';
                data = [];
            }

            if (typeof parsed.transcription != "undefined") {
                text += ' ' + parsed.transcription;
            }

            if (typeof parsed.data != "undefined") {
                console.log(parsed.data);
                data = [];

                for (let i = 0; i < parsed.data.length; i++) {
                    data.push(parsed.data[i].text);
                }
            }

            return {
                id: parsed.id,
                phone: parsed.phone,
                text: text.trim(),
                data: data,
            };
        });
    }

    render() {
        return (
            <div>
                { !this.state.phone &&
                    <div>&nbsp;</div>
                }
                { this.state.phone &&
                    <div><b>Звонит: { this.state.phone }</b></div>
                }
                <textarea
                    style = {{
                        width: "100%",
                        height: "100px",
                        marginTop: "10px",
                    }}
                    value = {this.state.text}
                    onChange = {() => {}}
                    placeholder = "Транскрипция разговора"
                />
                <ul>
                {
                    this.state.data.map((line, index) => <li key={ index }>{ line }</li>)
                }
                </ul>
            </div>
        );
    }
}
