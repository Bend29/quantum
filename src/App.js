import React from 'react';
import { Log } from './Log.js';
import './App.css';

function App() {
  return (
    <div className="App">
    <p>
        Позвоните на номер <strong>+7 (499) 113 6086</strong> и говорите что-нибудь. Здесь должен появляться распознанный текст.
    </p>
    <Log />
    </div>
  );
}

export default App;
