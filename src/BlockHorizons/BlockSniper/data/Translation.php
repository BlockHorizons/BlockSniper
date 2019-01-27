<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\data;

use function count;
use function explode;
use function is_array;
use function vsprintf;

class Translation{

	/**
	 * These constants are AUTOMATICALLY GENERATED.
	 *
	 * Do NOT edit by hand.
	 */
	public const COMMANDS_COMMON_WARNING_PREFIX = "commands.common.warning-prefix";
	public const COMMANDS_COMMON_INVALID_SENDER = "commands.common.invalid-sender";
	public const COMMANDS_COMMON_NO_PERMISSION = "commands.common.no-permission";
	public const COMMANDS_BRUSH_DESCRIPTION = "commands.brush.description";
	public const COMMANDS_BLOCKSNIPER_DESCRIPTION = "commands.blocksniper.description";
	public const COMMANDS_BLOCKSNIPER_INFO = "commands.blocksniper.info";
	public const COMMANDS_BLOCKSNIPER_VERSION = "commands.blocksniper.version";
	public const COMMANDS_BLOCKSNIPER_ORGANISATION = "commands.blocksniper.organisation";
	public const COMMANDS_BLOCKSNIPER_AUTHORS = "commands.blocksniper.authors";
	public const COMMANDS_BLOCKSNIPER_TARGET_API = "commands.blocksniper.target-api";
	public const COMMANDS_BLOCKSNIPER_RELOAD = "commands.blocksniper.reload";
	public const COMMANDS_REDO_DESCRIPTION = "commands.redo.description";
	public const COMMANDS_REDO_NO_REDO = "commands.redo.no-redo";
	public const COMMANDS_REDO_SUCCESS = "commands.redo.success";
	public const COMMANDS_UNDO_DESCRIPTION = "commands.undo.description";
	public const COMMANDS_UNDO_NO_UNDO = "commands.undo.no-undo";
	public const COMMANDS_UNDO_SUCCESS = "commands.undo.success";
	public const COMMANDS_CLONE_DESCRIPTION = "commands.clone.description";
	public const COMMANDS_CLONE_COPY_SUCCESS = "commands.clone.copy.success";
	public const COMMANDS_CLONE_TEMPLATE_MISSING_NAME = "commands.clone.template.missing-name";
	public const COMMANDS_CLONE_TEMPLATE_SUCCESS = "commands.clone.template.success";
	public const COMMANDS_CLONE_SCHEMATIC_MISSING_NAME = "commands.clone.schematic.missing-name";
	public const COMMANDS_CLONE_SCHEMATIC_SUCCESS = "commands.clone.schematic.success";
	public const COMMANDS_PASTE_DESCRIPTION = "commands.paste.description";
	public const COMMANDS_PASTE_COPY_NO_COPIES = "commands.paste.copy.no-copies";
	public const COMMANDS_PASTE_COPY_SUCCESS = "commands.paste.copy.success";
	public const COMMANDS_PASTE_TEMPLATE_NONEXISTENT = "commands.paste.template.nonexistent";
	public const COMMANDS_PASTE_TEMPLATE_SUCCESS = "commands.paste.template.success";
	public const COMMANDS_PASTE_SCHEMATIC_NONEXISTENT = "commands.paste.schematic.nonexistent";
	public const COMMANDS_PASTE_SCHEMATIC_SUCCESS = "commands.paste.schematic.success";
	public const UI_PRESET_MENU_TITLE = "ui.preset-menu.title";
	public const UI_PRESET_MENU_SUBTITLE = "ui.preset-menu.subtitle";
	public const UI_PRESET_MENU_CREATE = "ui.preset-menu.create";
	public const UI_PRESET_MENU_DELETE = "ui.preset-menu.delete";
	public const UI_PRESET_MENU_SELECT = "ui.preset-menu.select";
	public const UI_PRESET_MENU_LIST = "ui.preset-menu.list";
	public const UI_PRESET_MENU_EXIT = "ui.preset-menu.exit";
	public const UI_PRESET_EDIT_TITLE = "ui.preset-edit.title";
	public const UI_PRESET_EDIT_NAME = "ui.preset-edit.name";
	public const UI_PRESET_SELECTION_TITLE = "ui.preset-selection.title";
	public const UI_PRESET_SELECTION_SUBTITLE = "ui.preset-selection.subtitle";
	public const UI_PRESET_DELETION_TITLE = "ui.preset-deletion.title";
	public const UI_PRESET_DELETION_SUBTITLE = "ui.preset-deletion.subtitle";
	public const UI_PRESET_LIST_TITLE = "ui.preset-list.title";
	public const UI_PRESET_LIST_SUBTITLE = "ui.preset-list.subtitle";
	public const UI_PRESET_CREATION_TITLE = "ui.preset-creation.title";
	public const UI_PRESET_CREATION_NAME = "ui.preset-creation.name";
	public const UI_MAIN_MENU_TITLE = "ui.main-menu.title";
	public const UI_MAIN_MENU_SUBTITLE = "ui.main-menu.subtitle";
	public const UI_MAIN_MENU_BRUSH = "ui.main-menu.brush";
	public const UI_MAIN_MENU_TREE = "ui.main-menu.tree";
	public const UI_MAIN_MENU_CONFIG = "ui.main-menu.config";
	public const UI_MAIN_MENU_PRESETS = "ui.main-menu.presets";
	public const UI_MAIN_MENU_GLOBAL_BRUSH = "ui.main-menu.global-brush";
	public const UI_MAIN_MENU_EXIT = "ui.main-menu.exit";
	public const UI_BRUSH_MENU_TITLE = "ui.brush-menu.title";
	public const UI_BRUSH_MENU_SIZE = "ui.brush-menu.size";
	public const UI_BRUSH_MENU_SHAPE = "ui.brush-menu.shape";
	public const UI_BRUSH_MENU_TYPE = "ui.brush-menu.type";
	public const UI_BRUSH_MENU_MODE_DESCRIPTION = "ui.brush-menu.mode.description";
	public const UI_BRUSH_MENU_MODE_BRUSH = "ui.brush-menu.mode.brush";
	public const UI_BRUSH_MENU_MODE_SELECTION = "ui.brush-menu.mode.selection";
	public const UI_BRUSH_MENU_HOLLOW = "ui.brush-menu.hollow";
	public const UI_BRUSH_MENU_DECREMENT = "ui.brush-menu.decrement";
	public const UI_BRUSH_MENU_HEIGHT = "ui.brush-menu.height";
	public const UI_BRUSH_MENU_WIDTH = "ui.brush-menu.width";
	public const UI_BRUSH_MENU_LENGTH = "ui.brush-menu.length";
	public const UI_BRUSH_MENU_BLOCKS = "ui.brush-menu.blocks";
	public const UI_BRUSH_MENU_OBSOLETE = "ui.brush-menu.obsolete";
	public const UI_BRUSH_MENU_BIOME = "ui.brush-menu.biome";
	public const UI_BRUSH_MENU_TREE = "ui.brush-menu.tree";
	public const UI_CONFIGURATION_MENU_TITLE = "ui.configuration-menu.title";
	public const UI_CONFIGURATION_MENU_LANGUAGE = "ui.configuration-menu.language";
	public const UI_CONFIGURATION_MENU_BRUSH_ITEM = "ui.configuration-menu.brush-item";
	public const UI_CONFIGURATION_MENU_SELECTION_ITEM = "ui.configuration-menu.selection-item";
	public const UI_CONFIGURATION_MENU_MAX_BRUSH_SIZE = "ui.configuration-menu.max-brush-size";
	public const UI_CONFIGURATION_MENU_MIN_ASYNC_SIZE = "ui.configuration-menu.min-async-size";
	public const UI_CONFIGURATION_MENU_MAX_REVERTS = "ui.configuration-menu.max-reverts";
	public const UI_CONFIGURATION_MENU_RESET_DECREMENT_BRUSH = "ui.configuration-menu.reset-decrement-brush";
	public const UI_CONFIGURATION_MENU_SAVE_BRUSH = "ui.configuration-menu.save-brush";
	public const UI_CONFIGURATION_MENU_DROP_PLANTS = "ui.configuration-menu.drop-plants";
	public const UI_CONFIGURATION_MENU_SESSION_TIMEOUT_TIME = "ui.configuration-menu.session-timeout-time";
	public const UI_CONFIGURATION_MENU_AUTO_GUI = "ui.configuration-menu.auto-gui";
	public const UI_CONFIGURATION_MENU_COOLDOWN = "ui.configuration-menu.cooldown";
	public const UI_CONFIGURATION_MENU_MYPLOT_SUPPORT = "ui.configuration-menu.myplot-support";
	public const UI_TREE_MENU_TITLE = "ui.tree-menu.title";
	public const UI_TREE_MENU_TRUNK_HEIGHT = "ui.tree-menu.trunk-height";
	public const UI_TREE_MENU_TRUNK_WIDTH = "ui.tree-menu.trunk-width";
	public const UI_TREE_MENU_MAX_BRANCH_LENGTH = "ui.tree-menu.max-branch-length";
	public const UI_TREE_MENU_TRUNK_BLOCKS = "ui.tree-menu.trunk-blocks";
	public const UI_TREE_MENU_LEAVES_BLOCKS = "ui.tree-menu.leaves-blocks";
	public const UI_TREE_MENU_LEAVES_CLUSTER_SIZE = "ui.tree-menu.leaves-cluster-size";
	public const LOG_LANGUAGE_AUTO_SELECTED = "log.language.auto-selected";
	public const LOG_LANGUAGE_USAGE = "log.language.usage";
	public const LOG_LANGUAGE_SELECTED = "log.language.selected";
	public const LOG_BRUSH_RESTORED = "log.brush.restored";
	public const LOG_BRUSH_ALL_RESTORED = "log.brush.all-restored";
	public const LOG_PRESETS_LOADED = "log.presets.loaded";
	public const LOG_PRESETS_ALL_LOADED = "log.presets.all-loaded";
	public const LOG_RELOAD_START = "log.reload.start";
	public const LOG_RELOAD_FINISH = "log.reload.finish";
	public const BRUSH_STATE_READY = "brush.state.ready";
	public const BRUSH_STATE_DONE = "brush.state.done";
	public const BRUSH_SELECTION_FIRST = "brush.selection.first";
	public const BRUSH_SELECTION_SECOND = "brush.selection.second";
	public const BRUSH_SELECTION_ERROR = "brush.selection.error";
	public const BRUSH_SHAPE_CUBE = "brush.shape.cube";
	public const BRUSH_SHAPE_CUBOID = "brush.shape.cuboid";
	public const BRUSH_SHAPE_SPHERE = "brush.shape.sphere";
	public const BRUSH_SHAPE_CYLINDER = "brush.shape.cylinder";
	public const BRUSH_SHAPE_ELLIPSOID = "brush.shape.ellipsoid";
	public const BRUSH_TYPE_BIOME = "brush.type.biome";
	public const BRUSH_TYPE_CLEANENTITIES = "brush.type.cleanentities";
	public const BRUSH_TYPE_CLEAN = "brush.type.clean";
	public const BRUSH_TYPE_DRAIN = "brush.type.drain";
	public const BRUSH_TYPE_EXPAND = "brush.type.expand";
	public const BRUSH_TYPE_FILL = "brush.type.fill";
	public const BRUSH_TYPE_FLATTENALL = "brush.type.flattenall";
	public const BRUSH_TYPE_FLATTEN = "brush.type.flatten";
	public const BRUSH_TYPE_LAYER = "brush.type.layer";
	public const BRUSH_TYPE_LEAFBLOWER = "brush.type.leafblower";
	public const BRUSH_TYPE_MELT = "brush.type.melt";
	public const BRUSH_TYPE_OVERLAY = "brush.type.overlay";
	public const BRUSH_TYPE_REPLACEALL = "brush.type.replaceall";
	public const BRUSH_TYPE_REPLACE = "brush.type.replace";
	public const BRUSH_TYPE_SNOWCONE = "brush.type.snowcone";
	public const BRUSH_TYPE_TOPLAYER = "brush.type.toplayer";
	public const BRUSH_TYPE_TREE = "brush.type.tree";

	/** @var string[] */
	private static $translations = [];
	/** @var TranslationData */
	private static $translationData;
	/** @var array */
	private $messageData = [];

	public function __construct(TranslationData $data){
		self::$translationData = $data;
		$this->messageData = $data->getMessages();
		$reflection = new \ReflectionClass(self::class);
		foreach($reflection->getConstants() as $constant => $value){
			if(($msg = $this->putMessage($value)) !== null){
				self::$translations[$value] = $msg;
			}
		}
	}

	/**
	 * @param string $key
	 *
	 * @return string
	 */
	private function putMessage(string $key) : ?string{
		$messages = $this->messageData;
		$path = explode(".", $key);
		$pathCount = count($path);

		$message = $messages[$path[0]];
		for($i = 1; $i < $pathCount; $i++){
			if(is_array($message)){
				if(!isset($message[$path[$i]])){
					return null;
				}
				$message = $message[$path[$i]];
			}
		}

		return $message;
	}

	/**
	 * @param string   $key
	 * @param string[] $params
	 *
	 * @return string
	 */
	public static function get(string $key, string... $params) : string{
		if(!isset(self::$translations[$key])){
			// We tried getting a key that did not exist, which means our language file is outdated. We regenerate it
			// and try again.
			self::$translationData->regenerateLanguageFile();
			return self::get($key, ...$params);
		}
		if(!empty($params)){
			return vsprintf(self::$translations[$key], $params);
		}

		return self::$translations[$key];
	}
}