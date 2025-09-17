# WebRotator

A single PHP page that opens one controlled browser window and rotates through a list of sites on a timer.  Built with and for **Nilesh Ramrattan, MBA**.  Coded by **ChatGPT** based on Nilesh’s input, requirements, and suggestions.

## Features

* Clean dark UI with controls, status, and a numbered playlist.
* One controlled window that navigates every N seconds.  Works when sites do not allow iframes.
* Progress bar and remaining time text.  Start, Pause, Resume, Next, Previous, Stop.
* Optional remember last index stored in local storage.
* Header logo on the right.  The script prefers `RamrattanLogo.png` and falls back to `RamrattanLogo.jpg` if present.

## Requirements

* PHP 7.4 or newer.  
* A browser that allows a user initiated popup for this page.

## Quick start

1. Upload **WebRotatorV1.14.php** to your server.  
2. Edit the `$urls` array near the top of the file to suit your playlist.  The default list contains major news sites.  
3. Optionally change `$intervalMs` to set the period in milliseconds.  
4. Visit the page.  Click **Start**.  Allow popups if prompted.

## Logo

Place a logo file in the same folder as the PHP file using one of these names:

* `RamrattanLogo.png`  preferred since it supports transparency.  
* `RamrattanLogo.jpg`  used if the PNG is not present.

If neither file is present, the logo area is hidden automatically.  

## Configuration points

* `$urls`  the ordered playlist.  
* `$intervalMs`  the period between navigations in milliseconds.  
* `$rememberLast`  if true the last index is saved and restored from local storage.

## Privacy and storage

This page writes one key to the browser: `url_rotator_idx` for the last index when the remember option is enabled.  No other client data is persisted.  No server data is stored.  

## Development

Run a very small lint check locally:

```bash
php -l WebRotatorV1.14.php
