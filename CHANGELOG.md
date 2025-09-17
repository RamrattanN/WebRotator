# Changelog

All notable changes to this project are documented here.  Dates are in local time.

## [1.15]
- Footer now shows an automatic configuration tag that changes when `$urls` or `$intervalMs` change.  
- Header logo is now a hyperlink to `https://Ramrattan.com` and opens in a new window.  

## [1.14] - Default news playlist
- Updated the default URL playlist to major news sites.  NBC News, ABC News, CBS News, Fox News, CNN, Reuters, AP News, The New York Times, The Wall Street Journal.
- Footer file reference and logo cache buster updated to 1.14.

## [1.13] - Robust logo detection
- The header logo is now selected server side.  The script uses RamrattanLogo.png if present, otherwise RamrattanLogo.jpg if present, otherwise the logo is hidden.
- Theme background token set to `#0f172a`.

## [1.12] - Logo source refactor
- Attempted to prefer a transparent PNG with a JPEG fallback using the picture element.  Some browsers did not fall back when the PNG was missing.  Replaced in 1.13.

## [1.11] - Baseline visual and docs
- Baseline with background `#0B1D39`.  Added header logo placement.  Added detailed inline comments.  Added initial README and changelog.
- Note.  An earlier prototype that only advanced a single window on a fixed interval is a separate project and is not part of WebRotator.

## [1.10] - UI polish
- Added progress bar and remaining time text.  Added remember last index using local storage.  General layout and spacing improvements.

## [1.9] - Pretty landing page
- Introduced the landing page with Controls, Status, and Playlist.  Implemented single controlled window rotation with Start, Pause, Resume, Next, Previous, and Stop.
