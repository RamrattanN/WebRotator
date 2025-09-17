# Contributing

Thank you for your interest in improving WebRotator.  Small, focused contributions are welcome.  Please read this guide before opening an issue or a pull request.

## Scope

WebRotator is a single PHP file that opens one controlled browser window and rotates through a list of sites on a timer.  The goal is to keep the footprint simple, the layout clean, and the code self contained.

## Ways to contribute

1. Report a bug with clear steps to reproduce and the environment where you observed it.  
2. Propose an improvement to the user experience or the visual design.  
3. Improve code clarity through comments and small refactors that do not change behavior.  
4. Add tests or checks that increase reliability, such as linting steps or workflow tweaks.

## Development setup

1. Clone the repository and create a new branch off main.  
2. Make your change in a minimal diff.  Keep the single file approach unless there is a compelling reason to split.  
3. Run a quick lint locally.

```bash
php -l WebRotatorV1.15.php
