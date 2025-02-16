let score = 0;
let secondsPlayed = 0;
//voor de 10 vragen per ronde
let counter = 0;
//voor de 5 rondes
let roundsCounter = 0;
let achievements = { easy: false, medium: false, hard: false, platinum: false };
//voor juiste vragen na elkaar
let streakCounter = 0;

//API params
const BASE_URL = "http://localhost/Stoom/public/api";
const urlParams = new URLSearchParams(window.location.search);
const apiKey = urlParams.get("apiKey");
const userId = urlParams.get("userId");
const hash = urlParams.get("hash");

function getCategory(event) {
  let button = document.getElementById(event.target.id);
  let htmlCollection = document.getElementsByClassName("category-btn");
  let arr = Array.from(htmlCollection);

  arr.forEach((element) => {
    element.classList.remove("selected");
  });
  if (button.tagName === "BUTTON") {
    button.classList.add("selected");
  }
}

function getDifficulty(event) {
  let button = document.getElementById(event.target.id);
  let htmlCollection = document.getElementsByClassName("difficulty-btn");
  let arr = Array.from(htmlCollection);

  arr.forEach((element) => {
    element.classList.remove("selected");
  });
  if (button.tagName === "BUTTON") {
    button.classList.add("selected");
  }
}

async function playGame() {
  if (roundsCounter > 0) {
    //om eventlisteners te verwijderen vanaf tweede ronde omdat anders
    //de eventlistener in elke ronde opnieuw wordt aangemaakt
    let container = document.querySelector("#answers-container");
    let clonedContainer = container.cloneNode(true);
    container.parentNode.replaceChild(clonedContainer, container);
    container = clonedContainer;

    let button = document.querySelector("#next-button");
    let clonedButton = button.cloneNode(true);
    button.parentNode.replaceChild(clonedButton, button);
    button = clonedButton;
  }

  let htmlCollection = document.getElementsByClassName("selected");
  let selectedFields = Array.from(htmlCollection);

  if (selectedFields.length < 2) {
    document.querySelector("#alert").innerText = "Select all fields";
    return;
  }
  document.querySelector("#play-button").disabled = true;
  document.querySelector("#play-button").style.background = "#efe4e4";

  let category = selectedFields[0].id;
  let difficulty = selectedFields[1].id;

  let questions = await getQuestions(category, difficulty);

  showQuestion(questions[counter]);
  //timer starten
  timer = setInterval(startTimer, 1000);

  document
    .querySelector("#answers-container")
    .addEventListener("click", function check(event) {
      //function naam gegeven om event door te kunnen geven
      checkAnswer(event, questions[counter]);
    });

  document.querySelector("#next-button").addEventListener("click", () => {
    showQuestion(questions[counter]);
  });
}

async function getQuestions(category, difficulty) {
  const BASE_URL = "https://opentdb.com/api.php?amount=10&category=";
  const response = await fetch(
    BASE_URL + category + "&difficulty=" + difficulty + "&type=multiple"
  );
  const data = await response.json();
  const questions = data.results;
  return questions;
}

function showQuestion(question) {
  resetAnswerButtons();
  document.querySelector("#question").innerText = replaceSpecialCharacters(
    question.question
  );
  let answers = question.incorrect_answers;
  answers.push(question.correct_answer);
  const shuffledAnswers = shuffleAnswers(answers);
  for (let i = 0; i < shuffledAnswers.length; i++) {
    document.querySelector("#answer" + (i + 1)).innerText =
      replaceSpecialCharacters(shuffledAnswers[i]);
  }
}

function shuffleAnswers(array) {
  //van internet raar algoritme
  for (let i = array.length - 1; i > 0; i--) {
    // Generate Random Index
    const j = Math.floor(Math.random() * (i + 1));

    // Swap elements
    [array[i], array[j]] = [array[j], array[i]];
  }
  return array;
}

function checkAnswer(event, question) {
  if (document.getElementById(event.target.id).tagName === "BUTTON") {
    document.querySelectorAll(".answer-btn").forEach((button) => {
      button.disabled = true;
    });

    let playerAnswer = event.target.innerText;
    let playerAnswerBlock = event.target.id;

    if (playerAnswer !== replaceSpecialCharacters(question.correct_answer)) {
      streakCounter = 0;
      document.getElementById(playerAnswerBlock).style.background = "red";
      //zet juiste vraag in het groen
      let answers = Array.from(document.getElementsByClassName("answer-btn"));
      answers.forEach((answer) => {
        if (
          answer.innerText == replaceSpecialCharacters(question.correct_answer)
        ) {
          document.getElementById(answer.id).style.background = "green";
        }
      });
    }

    if (playerAnswer === replaceSpecialCharacters(question.correct_answer)) {
      document.getElementById(playerAnswerBlock).style.background = "green";
      document.querySelector("#score-number").innerText =
        scoreCounter(question);
      checkAchievements(question.difficulty);
    }

    document.querySelector("#next-button").disabled = false;
    if (counter <= 9) {
      counter += 1;
    }
    if (counter > 9) {
      counterChecker(question);
    }
  }
}

function checkAchievements(difficulty) {
  let reward = null;
  let image = null;

  streakCounter += 1;
  if (streakCounter === 3 && achievements[difficulty] === false) {
    switch (difficulty) {
      case "easy":
        reward = "bronze";
        image =
          "http://localhost/stoom/games/quizgame/images/medaillebrons.png";
        achievements.easy = true;
        document.querySelector("#bronze").classList.remove("not-achieved");
        Swal.fire({
          title: "Hoera!",
          text: "Je hebt een bronzen medaille gewonnen",
          confirmButtonText: "OK",
        });

        saveAchievements(reward, image);
        break;

      case "medium":
        reward = "silver";
        image =
          "http://localhost/stoom/games/quizgame/images/medaillezilver.png";
        achievements.medium = true;
        document.querySelector("#silver").classList.remove("not-achieved");
        Swal.fire({
          title: "Hoera!",
          text: "Je hebt een zilveren medaille gewonnen",
          confirmButtonText: "OK",
        });

        saveAchievements(reward, image);
        break;

      case "hard":
        reward = "gold";
        image = "http://localhost/stoom/games/quizgame/images/medaillegoud.png";
        achievements.hard = true;
        document.querySelector("#gold").classList.remove("not-achieved");
        Swal.fire({
          title: "Hoera!",
          text: "Je hebt een gouden medaille gewonnen",
          confirmButtonText: "OK",
        });

        saveAchievements(reward, image);
        break;

      default:
        break;
    }
  }
  if (
    achievements.easy === true &&
    achievements.medium === true &&
    achievements.hard === true &&
    achievements.platinum === false
  ) {
    reward = "platinum";
    image = "http://localhost/stoom/games/quizgame/images/medailleplatinum.png";
    achievements.platinum = true;
    document.querySelector("#platinum").classList.remove("not-achieved");
    Swal.fire({
      title: "Hoera!",
      text: "Je hebt een platinum medaille gewonnen",
      confirmButtonText: "OK",
    });

    saveAchievements(reward, image);
  }
}

function scoreCounter(question) {
  const basePoints = 10;
  const easyMultiplier = 1;
  const mediumMultiplier = 2;
  const hardMultiplier = 5;

  switch (question.difficulty) {
    case "easy":
      score += basePoints * easyMultiplier;
      break;
    case "medium":
      score += basePoints * mediumMultiplier;
      break;
    case "hard":
      score += basePoints * hardMultiplier;
      break;
    default:
      score = score;
  }
  return score;
}

function counterChecker(question) {
  roundsCounter += 1;
  pauseTimer();
  if (roundsCounter < 5) {
    counter = 0;
    streakCounter = 0;
    //net gespeelde categorie en difficulty
    let selectedFields = Array.from(
      document.getElementsByClassName("selected")
    );
    //button van net gespeelde ronde
    let categoryButton = document.getElementById(selectedFields[0].id);

    document.querySelector("#play-button").disabled = false;
    document.querySelector("#play-button").style.background = "darkslategray";
    document.querySelector("#next-button").disabled = true;
    categoryButton.style.background = "#efe4e4";
    categoryButton.disabled = true;
    categoryButton.classList.remove("selected");

    Swal.fire({
      title: "Ronde voorbij",
      text: "Kies een nieuwe categorie",
      confirmButtonText: "OK",
    });
  }

  if (roundsCounter >= 5) {
    showPlayedTime();
    Swal.fire({
      title: "Spel is voorbij",
      text: "Je hebt " + score + " punten",
      confirmButtonText: "OK",
    });
  }
}

function resetAnswerButtons() {
  document.querySelectorAll(".answer-btn").forEach((button) => {
    button.disabled = false;
  });
  document.querySelectorAll(".answer-btn").forEach((button) => {
    button.style.background = "#658e8e";
  });
}

function replaceSpecialCharacters(string) {
  string = string.replace(/&quot;/g, '"');
  string = string.replace(/&#039;/g, "'");
  string = string.replace(/&amp;/g, "&");
  string = string.replace(/&ouml;/g, "ö");
  string = string.replace(/&rsquo;/g, "'");
  return string;
}

function startTimer() {
  secondsPlayed++;
}

function pauseTimer() {
  clearInterval(timer);
}

function showPlayedTime() {
  saveScores(secondsPlayed);
  //secondsplayed omzetten naar uren, minuten en seconden
  let hours = 0;
  let minutes = 0;
  let seconds = 0;

  if (secondsPlayed < 60) {
    seconds = secondsPlayed;
  }

  if (secondsPlayed >= 60 && secondsPlayed < 3600) {
    minutes = Math.floor(secondsPlayed / 60);
    seconds = secondsPlayed - minutes * 60;
  }

  if (secondsPlayed >= 3600) {
    let secondsLeft = secondsPlayed;
    hours = Math.floor(secondsPlayed / 3600);
    secondsLeft = secondsPlayed - hours * 3600;
    minutes = Math.floor(secondsLeft / 60);
    seconds = secondsLeft - minutes * 60;
  }

  document.querySelector("#time-played").innerText =
    "TIME PLAYED: " + hours + "h " + minutes + "m " + seconds + "s";
}

//--------------------------------------------API---------------------------------------------------

//achievements
async function getAchievements() {
  const response = await fetch(
    BASE_URL +
      "/achievement/get?" +
      "&apiKey=" +
      apiKey +
      "&userId=" +
      userId +
      "&hash=" +
      hash
  );
  const rewards = await response.json();
  fillAchievements(rewards);
}

function fillAchievements(rewards) {
  const rewardMap = {
    bronze: "easy",
    silver: "medium",
    gold: "hard",
    platinum: "platinum",
  };

  rewards.forEach((reward) => {
    if (reward in rewardMap) {
      achievements[rewardMap[reward]] = true;
    }
    const element = document.querySelector("#" + reward);
    if (element) {
      element.classList.remove("not-achieved");
    }
  });
}

async function saveAchievements(reward, image) {
  const today = new Date();
  const formattedDate = today.toISOString().split("T")[0]; // "YYYY-MM-DD"

  const postData = {
    name: reward,
    image: image,
    date: formattedDate,
    apiKey: apiKey,
    userId: Number(userId),
    hash: hash,
  };

  let response = await fetch(BASE_URL + "/achievement/save", {
    method: "POST",
    body: JSON.stringify(postData),
  });
  let data = await response.json();
}

//player///
async function getPlayer() {
  const response = await fetch(
    BASE_URL +
      "/player?" +
      "&apiKey=" +
      apiKey +
      "&userId=" +
      userId +
      "&hash=" +
      hash
  );
  const playerInfo = await response.json();
  showPlayerInfo(playerInfo);
}

function showPlayerInfo(playerInfo) {
  document.querySelector("#player-name").innerText = playerInfo.username;
  let link = "http://localhost/Stoom/public/" + playerInfo.profilePicture;
  document.querySelector("#player-img").src = link;
}

//SCORE

async function saveScores(secondsPlayed) {
  const today = new Date();
  const formattedDate = today.toISOString().split("T")[0]; // "YYYY-MM-DD"

  const postData = {
    score: score,
    time: secondsPlayed,
    date: formattedDate,
    apiKey: apiKey,
    userId: Number(userId),
    hash: hash,
  };

  let response = await fetch(BASE_URL + "/score/save", {
    method: "POST",
    body: JSON.stringify(postData),
  });
  let data = await response.json();
}

getAchievements();
getPlayer();
document
  .querySelector("#category-container")
  .addEventListener("click", (event) => getCategory(event));

document
  .querySelector("#difficulty-container")
  .addEventListener("click", (event) => getDifficulty(event));

document.querySelector("#play-button").addEventListener("click", playGame);
