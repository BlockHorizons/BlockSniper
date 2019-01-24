# Changelog

## [4.0.0] (For API 4.0.0)- Unreleased
### Added
- Added a new UI that opens after setting the brush shape, type and mode. This new UI contains brush properties
  specifically available for the combination of shape, type and mode set.
- Presets can now set tree properties in addition to other properties.
- A cooldown bar is added to limit the rate at which BlockSniper can be used. The duration is configurable.
- Modifications with the Regenerate brush type may now be undone.
  
### Changed
- The Brush UI no longer contains all available brush properties.
- The main menu no longer contains a Tree button.
- The tree properties now show when the new brush properties UI is opened.
- The preset creation window no longer holds all available properties. Instead, like the brush window, it now only shows
  brush properties that apply.
- It is no longer possible to do two or more brush modifications concurrently.

### Fixed
- Fixed the Clean type doing the opposite of what it is supposed to do.
- Fixed the Leafblower type not dropping items as expected, and fixed its complete failure when used in a size that
  would make it executed asynchronous.
- Fixed an error that would occur when using a brush with sphere or cylinder shape, with a size of 0.
- Fixed an error that would occur when using a brush that would attempt to place blocks outside of the world.
- Fixed right click selection on windows 10 causing multiple chat messages to be sent.
- Fixed a very rare rounding error that would cause the server to crash when doing a medium sized modification.
- Fixed schematics saved using BlockSniper causing blocks to be significantly misplaced.
- Fixed schematic pasting running out of memory quickly with bigger schematics.