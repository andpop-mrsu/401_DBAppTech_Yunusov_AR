import { openDB } from './idb.js';

let db;

export async function initDB() {
  db = await openDB('HangmanDB', 1, {
    upgrade(db) {
      if (!db.objectStoreNames.contains('games')) {
        const store = db.createObjectStore('games', {
          keyPath: 'id',
          autoIncrement: true
        });
        store.createIndex('date', 'date');
      }
    }
  });
}

export async function saveGame(data) {
  return db.put('games', {
    ...data,
    date: new Date().toISOString()
  });
}

export async function getAllGames() {
  return db.getAll('games');
}

export async function getGameById(id) {
  return db.get('games', Number(id));
}