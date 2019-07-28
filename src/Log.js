import React from 'react';

export class Log extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
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
            data.push(line);

            return {data: data};
        });
    }

    render() {
        return (
            <div>
            { this.state.data.map((line, index) => <p key={ index }>{ line }</p>) }
            </div>
        );
    }
}
