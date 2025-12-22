export default class View {
  constructor(onGuess, onRestart, onBack) {
    this.hangman = document.getElementById('hangman');
    this.wordEl = document.getElementById('word');
    this.message = document.getElementById('message');
    this.letters = document.getElementById('letters');
    this.restartBtn = document.getElementById('restart');

    this.leftPanel = document.getElementById('left-panel');
    this.rightPanel = document.getElementById('right-panel');
    this.fullArea = document.getElementById('full-area');

    this.restartBtn.addEventListener('click', onRestart);
    this.onBack = onBack;

    this.renderLetters(onGuess);
  }

  hideGamePanels() {
    this.leftPanel.style.display = 'none';
    this.rightPanel.style.display = 'none';
  }

  showGamePanels() {
    this.leftPanel.style.display = 'flex';
    this.rightPanel.style.display = 'flex';
  }

  renderLetters(onGuess) {
    this.letters.innerHTML = '';
    const alphabet = 'abcdefghijklmnopqrstuvwxyz';
    for (const letter of alphabet) {
      const btn = document.createElement('button');
      btn.textContent = letter;
      btn.disabled = false;
      btn.addEventListener('click', () => onGuess(letter, btn));
      this.letters.appendChild(btn);
    }
  }

  renderWord(masked) {
    this.wordEl.innerHTML = '';
    for (const c of masked) {
      const box = document.createElement('div');
      box.className = 'letter-box';
      box.textContent = c !== '_' ? c : '';
      this.wordEl.appendChild(box);
    }
  }

  renderHangman(wrong) {
    const pics = [
      `  +---+\n      |\n      |\n      |\n   =====`,
      `  +---+\n  0   |\n      |\n      |\n   =====`,
      `  +---+\n  0   |\n  |   |\n      |\n   =====`,
      `  +---+\n  0   |\n /|   |\n      |\n   =====`,
      `  +---+\n  0   |\n /|\\  |\n      |\n   =====`,
      `  +---+\n  0   |\n /|\\  |\n /    |\n   =====`,
      `  +---+\n  0   |\n /|\\  |\n / \\  |\n   =====`
    ];
    this.hangman.textContent = pics[Math.min(wrong, pics.length - 1)];
  }

  showMessage(msg) {
    this.message.textContent = msg;
  }

  disableLetter(btn) {
    btn.disabled = true;
  }

  disableAllLetters() {
    this.letters.querySelectorAll('button').forEach(b => b.disabled = true);
  }

  revealWord(word) {
    this.wordEl.innerHTML = '';
    for (const c of word) {
      const box = document.createElement('div');
      box.className = 'letter-box';
      box.textContent = c;
      this.wordEl.appendChild(box);
    }
  }

  renderFull(contentHtml) {
    this.hideGamePanels();
    this.fullArea.style.display = 'block';
    this.fullArea.innerHTML = `
      <button id="back-btn" style="margin-bottom:20px;"> ⮜ Back </button>
      ${contentHtml}
    `;
    document.getElementById('back-btn').onclick = this.onBack;
  }

renderInfo() {
  const html = `
    <h2>Hangman App — Информация и возможности</h2>
    <p>Эта игра «Виселица» позволяет угадывать слова по буквам.</p>
    <h3>Режимы игры:</h3>
    <ul>
      <li><b>New Game:</b> Начать новую игру с случайным словом.</li>
      <li><b>Saved Games:</b> Просмотр списка всех сыгранных игр с результатами.</li>
      <li><b>Replay:</b> Воспроизвести историю ходов в игре.</li>
      <li><b>Info:</b> Показать эту справку с описанием возможностей.</li>
    </ul>

    <h3>Как играть:</h3>
    <ul>
      <li>Нажимайте на буквы в алфавите, чтобы угадывать слово.</li>
      <li>Если буква есть в слове — она откроется в слове.</li>
      <li>Если буквы нет — отображается новая часть виселицы.</li>
      <li>Игра заканчивается, когда слово полностью угадано или виселица достроена.</li>
    </ul>

    <h3>Сохранение и статистика:</h3>
    <ul>
      <li>После окончания игры все попытки игрока автоматически сохраняются в браузере.</li>
      <li>Можно просмотреть все сохранённые игры и историю ходов каждой игры.</li>
      <li>В списке игр отображается ID игры, слово и список всех введённых букв.</li>
    </ul>
  `;

  this.renderFull(html);
}


  renderSavedGames(games, onReplayClick) {
    if (!games.length) {
      return this.renderFull('<p>No saved games yet.</p>');
    }

    let html = `
      <h2>Saved Games</h2>
      <table>
        <thead>
          <tr><th>ID</th><th>Player</th><th>Word</th><th>Replay</th></tr>
        </thead>
        <tbody>
    `;
    games.forEach(g => {
      html += `<tr>
        <td>${g.id}</td>
        <td>${g.player}</td>
        <td>${g.word}</td>
        <td><button class="menu-btn" data-game='${JSON.stringify(g)}'>Replay</button></td>
      </tr>`;
    });
    html += '</tbody></table>';
    this.renderFull(html);

    this.fullArea.querySelectorAll('button[data-game]').forEach(btn => {
      btn.onclick = () => onReplayClick(btn.dataset.game);
    });
  }

  renderReplay(gameData, info, onBack) {
  let html = `
    <h2>Replay Game #${info.id}</h2>
    <table>
      <thead>
        <tr><th>Player</th><th>Word</th></tr>
      </thead>
      <tbody>
        <tr>
          <td>${info.player}</td>
          <td>${info.word}</td>
        </tr>
      </tbody>
    </table>
  `;

  if(gameData.length){
    html += `
      <h3>History of guesses</h3>
      <table class="guess-history"> 
        <thead>
          <tr><th>Letter</th><th>Result</th></tr>
        </thead>
        <tbody>
    `;
    gameData.forEach(h => {
      let color = h.result === 'ok' ? 'green' : (h.result === 'miss' ? 'red' : 'orange');
      html += `<tr>
        <td>${h.letter}</td>
        <td style="color:${color}">${h.result}</td>
      </tr>`;
    });
    html += '</tbody></table>';
  }

  this.renderFull(html);
  const backBtn = document.getElementById('back-btn');
  backBtn.onclick = onBack;
  }
}