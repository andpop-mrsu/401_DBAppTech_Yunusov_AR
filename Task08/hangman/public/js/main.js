import Game from './Game.js';
import View from './View.js';
import { createGame, addStep, getAllGames, getGameById } from './db.js';

const playerInput = document.getElementById("player-name");

let game;
let currentGameId = null;
const view = new View(handleGuess, startGame, () => {
  view.fullArea.style.display = 'none';
  view.showGamePanels();
});

document.getElementById("btn-info").onclick = () => view.renderInfo();
document.getElementById("btn-list").onclick = async () => {
  const games = await getAllGames();
  view.renderSavedGames(games, renderReplay);
};

async function startGame() {
  const words = ['planet', 'silver', 'castle', 'garden', 'bridge', 'school'];
  const randomWord = words[Math.floor(Math.random() * words.length)];
  game = new Game(randomWord);
  currentGameId = null;

  view.fullArea.style.display = 'none';
  view.showGamePanels();
  view.renderLetters(handleGuess);
  view.renderWord(game.getMaskedWord());
  view.renderHangman(game.getWrongCount());
  view.showMessage('Start guessing!');
}

async function handleGuess(letter, btn) {
  const result = game.guess(letter);
  view.disableLetter(btn);
  view.renderWord(game.getMaskedWord());
  view.renderHangman(game.getWrongCount());

  if (result === 'ok') view.showMessage(`Good job! '${letter}' is in the word.`);
  if (result === 'miss') view.showMessage(`Sorry, '${letter}' is not in the word.`);
  if (result === 'repeat') view.showMessage(`You already tried '${letter}'.`);

  const player = playerInput.value.trim() || "Anonymous";

  if (!currentGameId) {
    const gameData = {
      player,
      word: game.getWord(),
      won: game.isWon(),
      wrong: game.getWrongCount(),
      history: game.getHistory()
    };
    const res = await createGame(gameData);
    currentGameId = res.id;
  } else {
    const lastStep = game.getHistory().slice(-1)[0];
    await addStep(currentGameId, lastStep);
  }

  if (game.isWon() || game.isLost()) {
    if (game.isWon()) {
      view.showMessage(`🎉 You guessed it! The word was '${game.getWord()}'`);
    } else if (game.isLost()) {
      view.showMessage(`💀 You lost! The word was '${game.getWord()}'`);
    }
    view.disableAllLetters();
  }
}

async function renderSavedGames() {
  const games = await getAllGames();
  view.renderSavedGames(games, renderReplay);
}

async function renderReplay(data) {
  const info = JSON.parse(data);
  const g = await getGameById(info.id);
  if (!g) {
    alert("Game not found");
    return;
  }
  view.renderReplay(g, info, renderSavedGames);
}

startGame();