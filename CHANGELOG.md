# Changelog

## [4.0.0] (For API 4.0.0)- Unreleased
### Added
- Added a new UI that opens after setting the brush shape, type and mode. This new UI contains brush properties
  specifically available for the combination of shape, type and mode set.
- Presets can now set tree properties in addition to other properties.
- A cooldown bar is added to limit the rate at which BlockSniper can be used. The duration is configurable.
- Modifications with the Regenerate brush type may now be undone.
- Added a new Ellipsoid shape with width, length and height properties.
- Added width, length and height properties for cylinders and cuboids.
- Added a new Smooth type which gets rid of rough edges and holes in the terrain.
- Added brush to item binding. Use /b bind and /b unbind to bind a new brush to an item.
    - Bound brushes can be named and display the shape and type they have in the name.
  
### Changed
- The Brush UI no longer contains all available brush properties.
- The tree properties now show when the new brush properties UI is opened.
- The preset creation window no longer holds all available properties. Instead, like the brush window, it now only shows
  brush properties that apply.
- It is no longer possible to do two or more brush modifications concurrently.
- The maximum brush distance now depends on the view distance of a player.
- The language files will now be automatically updated if it is found not to be up to date. It will no longer show a
  message prompting you to let the file regenerate.
- The Expand type now uses brush blocks instead of the blocks below it to place new blocks, resulting in a much better
  effect.
- The TopLayer type no longer uses the `Brush Height` property for the width of the layer. It now uses a new 
  `Layer Width` property

### Removed
- The main menu no longer contains a Tree button.
- The configuration menu no longer has a reload button. This is done automatically.

### Fixed
- Fixed the brush UI not showing the translated brush shapes and types.
- Fixed the Clean type doing the opposite of what it is supposed to do.
- Fixed the Leafblower type not dropping items as expected, and fixed its complete failure when used in a size that
  would make it executed asynchronous.
- Fixed an error that would occur when using a brush with sphere or cylinder shape, with a size of 0.
- Fixed an error that would occur when using a brush that would attempt to place blocks outside of the world.
- Fixed right click selection on windows 10 causing multiple chat messages to be sent.
- Fixed a very rare rounding error that would cause the server to crash when doing a medium sized modification.
- Fixed schematics saved using BlockSniper causing blocks to be significantly misplaced.
- Fixed schematic pasting running out of memory quickly with bigger schematics.