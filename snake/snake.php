<?php

function gameOver($snake) {
  if ($snake->tail > 5) {
    // If the trail is greater than 5 then check for end condition.
    for ($i = 1; $i < count($snake->trail); $i++) {
      if ($snake->trail[$i][0] == $snake->positionX && $snake->trail[$i][1] == $snake->positionY) {
        die('dead :(');
      }
    }
  }
}

function move($snake) {
  // Move the snake.
  $snake->positionX += $snake->movementX;
  $snake->positionY += $snake->movementY;

  // Wrap the snake around the boundaries of the board.
  if ($snake->positionX < 0) {
    $snake->positionX = $snake->width - 1;
  }
  if ($snake->positionX > $snake->width - 1) {
    $snake->positionX = 0;
  }
  if ($snake->positionY < 0) {
    $snake->positionY = $snake->height - 1;
  }
  if ($snake->positionY > $snake->height - 1) {
    $snake->positionY = 0;
  }

  // Add to the snakes trail at the front.
  array_unshift($snake->trail, [$snake->positionX, $snake->positionY]);

  // Remove a block from the end of the snake (but keep correct length).
  if (count($snake->trail) > $snake->tail) {
    array_pop($snake->trail);
  }

  if ($snake->appleX == $snake->positionX && $snake->appleY == $snake->positionY) {
    // The snake has eaten an apple.
    $snake->tail++;
    if ($snake->speed > 2000) {
      // Increase the speed of the game.
      $snake->speed = $snake->speed - ($snake->tail * ($snake->width / $snake->height + 10));
    }
    // Figure out a different place for the apple.
    $appleX = rand(0, $snake->width - 1);
    $appleY = rand(0, $snake->height - 1);
    while (array_search([$appleX, $appleY], $snake->trail) !== FALSE) {
      $appleX = rand(0, $snake->width - 1);
      $appleY = rand(0, $snake->height - 1);
    }
    $snake->appleX = $appleX;
    $snake->appleY = $appleY;
  }
}

function renderGame($snake) {
  $output = '';

  for ($i = 0; $i < $snake->width; $i++) {
    for ($j = 0; $j < $snake->height; $j++) {
      if ($snake->appleX == $i && $snake->appleY == $j) {
        $cell = '0';
      }
      else {
        $cell = '.';
      }
      foreach ($snake->trail as $trail) {
        if ($trail[0] == $i && $trail[1] == $j) {
          $cell = 'X';
        }
      }
      $output .= $cell;
    }
    $output .= PHP_EOL;
  }

  $output .= PHP_EOL;

  return $output;
}

function direction($stdin, $snake) {
  // Listen to the button being pressed.
  $key = fgets($stdin);
  if ($key) {
    $key = translateKeypress($key);
    switch ($key) {
      case "UP":
        $snake->movementX = -1;
        $snake->movementY = 0;
        break;
      case "DOWN":
        $snake->movementX = 1;
        $snake->movementY = 0;
        break;
      case "RIGHT":
        $snake->movementX = 0;
        $snake->movementY = 1;
        break;
      case "LEFT":
        $snake->movementX = 0;
        $snake->movementY = -1;
        break;
     }
  }
}

function translateKeypress($string) {
  switch ($string) {
    case "\033[A":
      return "UP";
    case "\033[B":
      return "DOWN";
    case "\033[C":
      return "RIGHT";
    case "\033[D":
      return "LEFT";
    case "\n":
      return "ENTER";
    case " ":
      return "SPACE";
    case "\010":
    case "\177":
      return "BACKSPACE";
    case "\t":
      return "TAB";
    case "\e":
      return "ESC";
   }
  return $string;
}

class Snake {
  public $width = 0;
  public $height = 0;

  public $positionX = 00;
  public $positionY = 00;

  public $appleX = 15;
  public $appleY = 15;

  public $movementX = 0;
  public $movementY = 0;

  public $trail = [];
  public $tail = 5;

  public $speed = 100000;

  public function __construct($width, $height) {
    $this->width = $width;
    $this->height = $height;

    $this->positionX = rand(0, $width - 1);
    $this->positionY = rand(0, $height - 1);

    $appleX = rand(0, $width - 1);
    $appleY = rand(0, $height - 1);

    while (array_search([$appleX, $appleY], $this->trail) !== FALSE) {
      $appleX = rand(0, $width - 1);
      $appleY = rand(0, $height - 1);
    }
    $this->appleX = $appleX;
    $this->appleY = $appleY;
  }

  public function __toString() {
    $output = '';
    $output .= 'positionX:' . $this->positionX . PHP_EOL;
    $output .= 'positionY:' . $this->positionY . PHP_EOL;
    $output .= 'appleX:' . $this->appleX . PHP_EOL;
    $output .= 'appleY:' . $this->appleY . PHP_EOL;
    $output .= 'movementX:' . $this->movementX . PHP_EOL;
    $output .= 'movementY:' . $this->movementY . PHP_EOL;
    $output .= 'tail:' . $this->tail . PHP_EOL;
    $output .= 'speed:' . $this->speed . PHP_EOL;
    return $output;
  }
}

$width = 20;
$height = 30;
$snake = new Snake($width, $height);

system('stty cbreak -echo');
$stdin = fopen('php://stdin', 'r');
stream_set_blocking($stdin, 0);

while (1) {
  system('clear');
  echo 'Level: ' . $snake->tail . PHP_EOL;
  direction($stdin, $snake);
  move($snake);
  echo renderGame($snake);
  gameOver($snake);
  usleep($snake->speed);
}
//project sample by Ka Macoy