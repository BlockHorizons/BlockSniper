# === 4.0.0 ===

### New Features
- Added a new UI that opens after setting the brush shape, type and mode. This new UI contains brush properties
  specifically available for the combination of shape, type and mode set.
  
### Behavioural Changes
- The Brush UI no longer contains all available brush properties.
- The main menu no longer contains a Tree button.
- The tree properties now show when the new brush properties UI is opened.

### Bug Fixes
- Fixed the Clean type doing the opposite of what it is supposed to do.
- Fixed the Leafblower type not dropping items as expected, and fixed its complete failure when used in a size that
  would make it executed asynchronous.
- Fixed an error that would occur when using a brush with sphere or cylinder shape, with a size of 0.
- Fixed an error that would occur when using a brush that would attempt to place blocks outside of the world.

### For PocketMine-MP API: 4.0.0