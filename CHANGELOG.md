# Changelog

## [4.0.0] (For API 4.0.0)- Unreleased
### Added
- Added a new UI that opens after setting the brush shape, type and mode. This new UI contains brush properties specifically available for the combination of shape, type and mode set.
- Presets can now set tree properties in addition to other properties.
- A cooldown bar is added to limit the rate at which BlockSniper can be used. The duration is configurable.
- Modifications with the Regenerate brush type may now be undone.
- Added a new Ellipsoid shape with width, length and height properties.
- Added width, length and height properties for cylinders and cuboids.
- Added a new Smooth type which gets rid of rough edges and holes in the terrain.
- Added brush to item binding. Use /b bind and /b unbind to bind a new brush to an item.
    - Bound brushes can be named and display the shape and type they have in the name.
    - Bound brushes are saved when the server restarts.
- Added a message if brush/obsolete blocks could not be resolved instead of defaulting to 0 silently.
- The duration a modification took is now sent upon the finishing of the modification. Note that this is not entirely accurate, in particular for smaller modifications.
- Added a new Replace Target type which replaces all blocks with the same type as the target block with the blocks set as brush blocks.
- Added a new Plant type which places brush blocks on top of all soil blocks set in the brush.
- Added an in-game changelog that shows up the first time you use a new version of BlockSniper. Note that it does not show up the first time a player starts using BlockSniper on a server.
- Added a new Changelog menu that can be used to view past changelogs and changelogs of newer versions.

### Changed
- The Brush UI no longer contains all available brush properties.
- The tree properties now show when the new brush properties UI is opened.
- It is no longer possible to do two or more brush modifications concurrently.
- The maximum brush distance now depends on the view distance of a player.
- The language files will now be automatically updated if it is found not to be up to date. It will no longer show a message prompting you to let the file regenerate.
- The Expand type now uses brush blocks instead of the blocks below it to place new blocks, resulting in a much better effect.
- The TopLayer type no longer uses the `Brush Height` property for the width of the layer. It now uses a new `Layer Width` property
- Brush blocks can now be selected using a readable name instead of using metadata, for example `spruce_sapling` instead of `sapling:1`
- The ReplaceAll type no longer ignores blocks such as torches, grass and other plants.
- The Copy clone type now copies relative to the center of the selection instead of the target block upon copying.

### Removed
- The main menu no longer contains a Tree button.
- The configuration menu no longer has a reload button. This is done automatically.
- The preset menu was removed in favour of the bound brushes that were added.

### Fixed
- Fixed cuboid and cylinder shapes not to be async/sync when they should be.
- Fixed the brush UI not showing the translated brush shapes and types.
- Fixed the Clean type doing the opposite of what it is supposed to do.
- Fixed the Leafblower type not dropping items as expected, and fixed its complete failure when used in a size that would make it executed asynchronous.
- Fixed an error that would occur when using a brush with sphere or cylinder shape, with a size of 0.
- Fixed an error that would occur when using a brush that would attempt to place blocks outside of the world.
- Fixed right click selection on windows 10 causing multiple chat messages to be sent.
- Fixed a very rare rounding error that would cause the server to crash when doing a medium sized modification.
- Fixed schematics saved using BlockSniper causing blocks to be significantly misplaced.
- Fixed schematic pasting running out of memory quickly with bigger schematics.
- Fixed [BlockSniper] being shown twice in console messages.
- Fixed the schematic pasted successfully message showing up before the schematic was even done pasting.