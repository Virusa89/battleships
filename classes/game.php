<?php

/**
 * Created by PhpStorm.
 * User: Nedelcho
 * Date: 9.11.2015 г.
 * Time: 19:44
 */
class game
{
    public $map = array();
    protected $shipPositions = array();
    protected $originalPositions = array();
    protected $totalTargets = 0;
    protected $shots = 0;

    static $ships = array(4, 3, 3, 2, 2, 2, 1, 1, 1, 1);
	static $signs = array('.', '.', '-', '*', 'X');
    static $orientation = array('vertical', 'horizontal');
    static $verticalDirections = array('left', 'right');
    static $horizontalDirections = array('up', 'down');
    static $mapX = 10;
    static $mapY = 10;

    public function __construct() {
		if (!isset($_SESSION['map'])) {
			$this
					->setTotals()
					->generateMapMatrix()
					->insertOnMap();
			$_SESSION['map'] = $this->map;
			$_SESSION['shipPositions'] = $this->shipPositions;
			$_SESSION['originalPositions'] = $this->shipPositions;
		}
		else {
			$this->setTotals();
			$this->map = $_SESSION['map'];
			$this->shipPositions = $_SESSION['shipPositions'];
			$this->originalPositions = $_SESSION['originalPositions'];
		}
		if('cli' !== php_sapi_name()) {
		}
		else {
			$this->consoleGame();
		}
    }

    public function setTotals() {
        $this->totalTargets = count(self::$ships);

        return $this;
    }

    public function generateMapMatrix() {
        for ($y = 0; $y < self::$mapY; $y++) {
            for ($x = 0; $x < self::$mapX; $x++) {
                $this->map[$y][$x] = 0;
            }
        }

        return $this;
    }

    public function insertOnMap() {
        foreach (self::$ships as $shipSize) {
            $this->addShip($shipSize);
        }

        return $this;
    }

    public function addShip($shipSize) {
        while (true) {
            $randomPosition = $this->getRandomPosition();
            $randomOrientation = self::$orientation[rand(0,1)];
            $randomDirection = $this->getRandomDirection($randomOrientation);

            if($this->canPositionShip($shipSize, $randomPosition, $randomDirection)) {
                $this->positionShip($shipSize, $randomPosition, $randomDirection);
                return true;
            }
        }
    }

    public function getRandomPosition() {
        while (true) {
            $randomX = rand(0, (self::$mapX - 1));
            $randomY = rand(0, (self::$mapY - 1));

            if ($this->map[$randomX][$randomY] === 0) {
                return array($randomY, $randomX);
            }
        }
    }

    public function getRandomDirection($orientation) {
        if ($orientation === 'horizontal') {
            return self::$horizontalDirections[rand(0, 1)];
        }
        else {
            return self::$verticalDirections[rand(0, 1)];
        }
    }

    public function canPositionShip($shipSize, $randomPosition, $randomDirection) {
        list($y, $x) = $randomPosition;

        for ($i = 0; $i < $shipSize; $i++) {
            if ($this->isOccupied($y, $x)) {
                return false;
            }

			if ($shipSize == 1) {
				if ($this->isOccupied($y, $x + 1)) {
					return false;
				}
				if ($this->isOccupied($y, $x - 1)) {
					return false;
				}
				if ($this->isOccupied($y+1, $x)) {
					return false;
				}
				if ($this->isOccupied($y-1, $x)) {
					return false;
				}
				if ($this->isOccupied($y+1, $x + 1)) {
					return false;
				}
				if ($this->isOccupied($y-1, $x + 1)) {
					return false;
				}
				if ($this->isOccupied($y+1, $x - 1)) {
					return false;
				}
				if ($this->isOccupied($y-1, $x - 1)) {
					return false;
				}
			}
			else {
				if ($randomDirection == 'left') {
					if ($this->isOccupied($y, $x - $i)) {
						return false;
					}
					if ($i==0) {
						if ($this->isOccupied($y, $x + 1)) {
							return false;
						}
						if ($this->isOccupied($y+1, $x)) {
							return false;
						}
						if ($this->isOccupied($y-1, $x)) {
							return false;
						}
						if ($this->isOccupied($y+1, $x + 1)) {
							return false;
						}
						if ($this->isOccupied($y-1, $x + 1)) {
							return false;
						}
					}
					else if ($i==($shipSize-1)) {
						if ($this->isOccupied($y, $x - 1)) {
							return false;
						}
						if ($this->isOccupied($y+1, $x)) {
							return false;
						}
						if ($this->isOccupied($y-1, $x)) {
							return false;
						}
						if ($this->isOccupied($y+1, $x - 1)) {
							return false;
						}
						if ($this->isOccupied($y-1, $x - 1)) {
							return false;
						}
					}
					else {
						if ($this->isOccupied($y+1, $x)) {
							return false;
						}
						if ($this->isOccupied($y-1, $x)) {
							return false;
						}
					}
				} else if ($randomDirection == 'right') {
					if ($this->isOccupied($y, $x + $i)) {
						return false;
					}
					if ($i==0) {
						if ($this->isOccupied($y, $x - 1)) {
							return false;
						}
						if ($this->isOccupied($y+1, $x)) {
							return false;
						}
						if ($this->isOccupied($y-1, $x)) {
							return false;
						}
						if ($this->isOccupied($y+1, $x - 1)) {
							return false;
						}
						if ($this->isOccupied($y-1, $x - 1)) {
							return false;
						}
					}
					else if ($i==($shipSize-1)) {
						if ($this->isOccupied($y, $x + 1)) {
							return false;
						}
						if ($this->isOccupied($y+1, $x)) {
							return false;
						}
						if ($this->isOccupied($y-1, $x)) {
							return false;
						}
						if ($this->isOccupied($y+1, $x + 1)) {
							return false;
						}
						if ($this->isOccupied($y-1, $x + 1)) {
							return false;
						}
					}
					else {
						if ($this->isOccupied($y+1, $x)) {
							return false;
						}
						if ($this->isOccupied($y-1, $x)) {
							return false;
						}
					}
				} else if ($randomDirection == 'up') {
					if ($this->isOccupied($y - $i, $x)) {
						return false;
					}
					if ($i==0) {
						if ($this->isOccupied($y + 1, $x)) {
							return false;
						}
						if ($this->isOccupied($y, $x+1)) {
							return false;
						}
						if ($this->isOccupied($y, $x-1)) {
							return false;
						}
						if ($this->isOccupied($y + 1, $x+1)) {
							return false;
						}
						if ($this->isOccupied($y + 1, $x-1)) {
							return false;
						}
					}
					else if ($i==($shipSize-1)) {
						if ($this->isOccupied($y-1, $x)) {
							return false;
						}
						if ($this->isOccupied($y, $x+1)) {
							return false;
						}
						if ($this->isOccupied($y, $x-1)) {
							return false;
						}
						if ($this->isOccupied($y-1, $x+1)) {
							return false;
						}
						if ($this->isOccupied($y-1, $x-1)) {
							return false;
						}
					}
					else {
						if ($this->isOccupied($y, $x+1)) {
							return false;
						}
						if ($this->isOccupied($y, $x-1)) {
							return false;
						}
					}
				} else if ($randomDirection == 'down') {
					if ($this->isOccupied($y + $i, $x)) {
						return false;
					}
					if ($i==0) {
						if ($this->isOccupied($y - 1, $x)) {
							return false;
						}
						if ($this->isOccupied($y, $x+1)) {
							return false;
						}
						if ($this->isOccupied($y, $x-1)) {
							return false;
						}
						if ($this->isOccupied($y - 1, $x+1)) {
							return false;
						}
						if ($this->isOccupied($y - 1, $x-1)) {
							return false;
						}
					}
					else if ($i==($shipSize-1)) {
						if ($this->isOccupied($y+1, $x)) {
							return false;
						}
						if ($this->isOccupied($y, $x+1)) {
							return false;
						}
						if ($this->isOccupied($y, $x-1)) {
							return false;
						}
						if ($this->isOccupied($y+1, $x+1)) {
							return false;
						}
						if ($this->isOccupied($y+1, $x-1)) {
							return false;
						}
					}
					else {
						if ($this->isOccupied($y, $x+1)) {
							return false;
						}
						if ($this->isOccupied($y, $x-1)) {
							return false;
						}
					}
				}
			}
		}
		
        return true;
    }

    public function isOccupied($y, $x) {
        return @$this->map[$y][$x] !== 0;
    }

    public function positionShip($shipSize, $randomPosition, $randomDirection) {
        list($y, $x) = $randomPosition;
        $shipID = count($this->shipPositions);

        for ($i = 0; $i < $shipSize; $i++) {
            if ($randomDirection == 'left') {
                $newY = $y;
                $newX = $x - $i;
            } else if ($randomDirection == 'right') {
                $newY = $y;
                $newX = $x + $i;
            } else if ($randomDirection == 'up') {
                $newY = $y - $i;
                $newX = $x;
            } else if ($randomDirection == 'down') {
                $newY = $y + $i;
                $newX = $x;
            }

            $this->fillPosition($newY, $newX);
            $this->shipPositions[$shipID][] = array($newY, $newX);
			$this->originalPositions[$shipID][] = array($newY, $newX);
        }

        return true;
    }

    public function fillPosition($y, $x) {
        return $this->map[$y][$x] = 1;
    }

    public function makeShot($y, $x) {
        $currentState = $this->map[$y][$x];
        $shipSunk = false;

        if ($currentState === 0) {
            $this->shots++;
        }
        else if ($currentState === 1) {
            $this->shots++;
            $this->totalTargets--;
            $shipSunk = $this->sunkShip($y, $x);
        }
        else if ($currentState === 3) {
            return false;
        }

        $newState = $currentState;
        $newState += 2;
		$newState = true === $shipSunk ? 4 : $newState;

        if($this->targetsLeft() === 0) {
            return 5;
        }

		$this->map[$y][$x] = $newState;
		$this->updateMap();
		$this->updatePositions();

        return $newState;
    }

    public function sunkShip($y, $x) {
        foreach($this->shipPositions as $shipId => $shipPosition) {
            if(0 === sizeof($shipPosition))
                continue;

            foreach($shipPosition as $positionId => $position) {
                list($shipY, $shipX) = $position;
                if($shipY == $y && $shipX == $x) {
                    unset($this->shipPositions[$shipId][$positionId]);
					if (sizeof($this->shipPositions[$shipId]) === 0) { $this->setAllStatusesForShip($shipId); }
                    return 0 === sizeof($this->shipPositions[$shipId]);
                }
            }
        }

        return false;
    }

	public function setAllStatusesForShip($shipId) {
		foreach ($this->originalPositions[$shipId] as $position) {
			$y = $position[0];
			$x = $position[1];
			$this->map[$y][$x] = 4;
		}
	}

	public function getCurrentMap() {
		return $this->map;
	}

	public function updateMap() {
		$_SESSION['map'] = $this->map;
	}

	public function updatePositions() {
		$_SESSION['shipPositions'] = $this->shipPositions;
	}

	public function consoleGame() {
		$this->consoleRenderMap();
		$this->writeMessage('Welcome to Battleships! Your oponent have 1x4, 2x3, 3x2 and 4x1 ships.');
		$this->consoleGameStart();
	}

	public function consoleGameStart() {
		$this->writeMessage('Enter ship coordinates 0-9,0-9: ');
		$coordinates = $this->readInput();

		if ($this->consoleCheckCoordinates($coordinates)) {
			$coordinatesArr = explode(',', $coordinates);
			list($y, $x) = $coordinatesArr;

			$newStatus = $this->makeShot(intval($y), intval($x));
			if ($newStatus == 2) {
				$message = "Ohhhhh you missed the target. Try again.";
			}
			else if ($newStatus == 3) {
				$message = "Wohoooo successful hit! Keep on!";
			}
			else if ($newStatus == 4) {
				$message = "Kabooom it's over with this ship. Bring on the next one!";
			}
			else if ($newStatus == 5) {
				$message = "Congratulations you sunk all your oponent's ships! You WON!!!";
			}
			else {
				$message = "Hmmmm this is embarrassing. The returned status makes no sense o.O";
			}
			$this->consoleRenderMap();
			$this->writeMessage($message);

			if ($newStatus == 5) {
				return false;
			}
			return $this->consoleGameStart();
		}
		else {
			return false;
		}
	}
	public function writeMessage($message) {
		fwrite(STDOUT, $message.PHP_EOL);

		return true;
	}

	public function readInput() {
		$input = fgets(STDIN);

		return $input;
	}

	public function consoleCheckCoordinates($coordinates) {
		$coordinatesArr = explode(',', $coordinates);
		$checkFlag = 1;

		foreach ($coordinatesArr as $coordinate) {
			if ($coordinate < 0 or $coordinate > 9) {
				$checkFlag = 0;
			}
		}

		if ($checkFlag == 1) { return true; }
		else { return false; }
	}

	public function consoleRenderMap() {
		$currentMap = $this->map;
		for ($i = 0; $i < self::$mapY; $i++) {
			$row = '';
			for ($j = 0; $j < self::$mapX; $j++) {
				$currentStatus = $currentMap[$i][$j];
				$sign = self::$signs[intval($currentStatus)];
				$row .= $sign;
			}
			$this->writeMessage($row);
		}
	}

	public function targetsLeft() {
		$count = 0;
		for ($i = 0; $i < count($this->shipPositions); $i++) {
			if (sizeof($this->shipPositions[$i]) > 0) { $count++; }
		}
		return $count;
	}
}