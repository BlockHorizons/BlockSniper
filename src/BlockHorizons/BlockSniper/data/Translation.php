<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\data;

class Translation{

	/**
	 * These constants are AUTOMATICALLY GENERATED.
	 *
	 * Do NOT edit by hand.
	 */
	const COMMANDS_COMMON_WARNING_PREFIX = "commands.common.warning-prefix";
	const COMMANDS_COMMON_INVALID_SENDER = "commands.common.invalid-sender";
	const COMMANDS_COMMON_NO_PERMISSION = "commands.common.no-permission";
	const COMMANDS_BRUSH_DESCRIPTION = "commands.brush.description";
	const COMMANDS_BLOCKSNIPER_DESCRIPTION = "commands.blocksniper.description";
	const COMMANDS_BLOCKSNIPER_INFO = "commands.blocksniper.info";
	const COMMANDS_BLOCKSNIPER_VERSION = "commands.blocksniper.version";
	const COMMANDS_BLOCKSNIPER_ORGANISATION = "commands.blocksniper.organisation";
	const COMMANDS_BLOCKSNIPER_AUTHORS = "commands.blocksniper.authors";
	const COMMANDS_BLOCKSNIPER_TARGET_API = "commands.blocksniper.target-api";
	const COMMANDS_BLOCKSNIPER_RELOAD = "commands.blocksniper.reload";
	const COMMANDS_REDO_DESCRIPTION = "commands.redo.description";
	const COMMANDS_REDO_NO_REDO = "commands.redo.no-redo";
	const COMMANDS_REDO_SUCCESS = "commands.redo.success";
	const COMMANDS_UNDO_DESCRIPTION = "commands.undo.description";
	const COMMANDS_UNDO_NO_UNDO = "commands.undo.no-undo";
	const COMMANDS_UNDO_SUCCESS = "commands.undo.success";
	const COMMANDS_CLONE_DESCRIPTION = "commands.clone.description";
	const COMMANDS_CLONE_COPY_SUCCESS = "commands.clone.copy.success";
	const COMMANDS_CLONE_TEMPLATE_MISSING_NAME = "commands.clone.template.missing-name";
	const COMMANDS_CLONE_TEMPLATE_SUCCESS = "commands.clone.template.success";
	const COMMANDS_CLONE_SCHEMATIC_MISSING_NAME = "commands.clone.schematic.missing-name";
	const COMMANDS_CLONE_SCHEMATIC_SUCCESS = "commands.clone.schematic.success";
	const COMMANDS_PASTE_DESCRIPTION = "commands.paste.description";
	const COMMANDS_PASTE_COPY_NO_COPIES = "commands.paste.copy.no-copies";
	const COMMANDS_PASTE_COPY_SUCCESS = "commands.paste.copy.success";
	const COMMANDS_PASTE_TEMPLATE_NONEXISTENT = "commands.paste.template.nonexistent";
	const COMMANDS_PASTE_TEMPLATE_SUCCESS = "commands.paste.template.success";
	const COMMANDS_PASTE_SCHEMATIC_NONEXISTENT = "commands.paste.schematic.nonexistent";
	const COMMANDS_PASTE_SCHEMATIC_SUCCESS = "commands.paste.schematic.success";
	const UI_PRESET_MENU_TITLE = "ui.preset-menu.title";
	const UI_PRESET_MENU_SUBTITLE = "ui.preset-menu.subtitle";
	const UI_PRESET_MENU_CREATE = "ui.preset-menu.create";
	const UI_PRESET_MENU_DELETE = "ui.preset-menu.delete";
	const UI_PRESET_MENU_SELECT = "ui.preset-menu.select";
	const UI_PRESET_MENU_LIST = "ui.preset-menu.list";
	const UI_PRESET_MENU_EXIT = "ui.preset-menu.exit";
	const UI_PRESET_EDIT_TITLE = "ui.preset-edit.title";
	const UI_PRESET_EDIT_NAME = "ui.preset-edit.name";
	const UI_PRESET_EDIT_SIZE = "ui.preset-edit.size";
	const UI_PRESET_EDIT_SHAPE = "ui.preset-edit.shape";
	const UI_PRESET_EDIT_TYPE = "ui.preset-edit.type";
	const UI_PRESET_EDIT_HOLLOW = "ui.preset-edit.hollow";
	const UI_PRESET_EDIT_DECREMENT = "ui.preset-edit.decrement";
	const UI_PRESET_EDIT_HEIGHT = "ui.preset-edit.height";
	const UI_PRESET_EDIT_PERFECT = "ui.preset-edit.perfect";
	const UI_PRESET_EDIT_BLOCKS = "ui.preset-edit.blocks";
	const UI_PRESET_EDIT_OBSOLETE = "ui.preset-edit.obsolete";
	const UI_PRESET_EDIT_BIOME = "ui.preset-edit.biome";
	const UI_PRESET_EDIT_TREE = "ui.preset-edit.tree";
	const UI_PRESET_SELECTION_TITLE = "ui.preset-selection.title";
	const UI_PRESET_SELECTION_SUBTITLE = "ui.preset-selection.subtitle";
	const UI_PRESET_DELETION_TITLE = "ui.preset-deletion.title";
	const UI_PRESET_DELETION_SUBTITLE = "ui.preset-deletion.subtitle";
	const UI_PRESET_LIST_TITLE = "ui.preset-list.title";
	const UI_PRESET_LIST_SUBTITLE = "ui.preset-list.subtitle";
	const UI_PRESET_CREATION_TITLE = "ui.preset-creation.title";
	const UI_PRESET_CREATION_NAME = "ui.preset-creation.name";
	const UI_PRESET_CREATION_SIZE = "ui.preset-creation.size";
	const UI_PRESET_CREATION_SHAPE = "ui.preset-creation.shape";
	const UI_PRESET_CREATION_TYPE = "ui.preset-creation.type";
	const UI_PRESET_CREATION_HOLLOW = "ui.preset-creation.hollow";
	const UI_PRESET_CREATION_DECREMENT = "ui.preset-creation.decrement";
	const UI_PRESET_CREATION_HEIGHT = "ui.preset-creation.height";
	const UI_PRESET_CREATION_PERFECT = "ui.preset-creation.perfect";
	const UI_PRESET_CREATION_BLOCKS = "ui.preset-creation.blocks";
	const UI_PRESET_CREATION_OBSOLETE = "ui.preset-creation.obsolete";
	const UI_PRESET_CREATION_BIOME = "ui.preset-creation.biome";
	const UI_PRESET_CREATION_TREE = "ui.preset-creation.tree";
	const UI_MAIN_MENU_TITLE = "ui.main-menu.title";
	const UI_MAIN_MENU_SUBTITLE = "ui.main-menu.subtitle";
	const UI_MAIN_MENU_BRUSH = "ui.main-menu.brush";
	const UI_MAIN_MENU_TREE = "ui.main-menu.tree";
	const UI_MAIN_MENU_CONFIG = "ui.main-menu.config";
	const UI_MAIN_MENU_PRESETS = "ui.main-menu.presets";
	const UI_MAIN_MENU_GLOBAL_BRUSH = "ui.main-menu.global-brush";
	const UI_MAIN_MENU_EXIT = "ui.main-menu.exit";
	const UI_BRUSH_MENU_TITLE = "ui.brush-menu.title";
	const UI_BRUSH_MENU_SIZE = "ui.brush-menu.size";
	const UI_BRUSH_MENU_SHAPE = "ui.brush-menu.shape";
	const UI_BRUSH_MENU_TYPE = "ui.brush-menu.type";
	const UI_BRUSH_MENU_HOLLOW = "ui.brush-menu.hollow";
	const UI_BRUSH_MENU_DECREMENT = "ui.brush-menu.decrement";
	const UI_BRUSH_MENU_HEIGHT = "ui.brush-menu.height";
	const UI_BRUSH_MENU_PERFECT = "ui.brush-menu.perfect";
	const UI_BRUSH_MENU_BLOCKS = "ui.brush-menu.blocks";
	const UI_BRUSH_MENU_OBSOLETE = "ui.brush-menu.obsolete";
	const UI_BRUSH_MENU_BIOME = "ui.brush-menu.biome";
	const UI_BRUSH_MENU_TREE = "ui.brush-menu.tree";
	const UI_CONFIGURATION_MENU_TITLE = "ui.configuration-menu.title";
	const UI_CONFIGURATION_MENU_AUTO_UPDATE = "ui.configuration-menu.auto-update";
	const UI_CONFIGURATION_MENU_LANGUAGE = "ui.configuration-menu.language";
	const UI_CONFIGURATION_MENU_BRUSH_ITEM = "ui.configuration-menu.brush-item";
	const UI_CONFIGURATION_MENU_MAX_BRUSH_SIZE = "ui.configuration-menu.max-brush-size";
	const UI_CONFIGURATION_MENU_MIN_ASYNC_SIZE = "ui.configuration-menu.min-async-size";
	const UI_CONFIGURATION_MENU_MAX_REVERTS = "ui.configuration-menu.max-reverts";
	const UI_CONFIGURATION_MENU_RESET_DECREMENT_BRUSH = "ui.configuration-menu.reset-decrement-brush";
	const UI_CONFIGURATION_MENU_SAVE_BRUSH = "ui.configuration-menu.save-brush";
	const UI_CONFIGURATION_MENU_DROP_PLANTS = "ui.configuration-menu.drop-plants";
	const UI_CONFIGURATION_MENU_SESSION_TIMEOUT_TIME = "ui.configuration-menu.session-timeout-time";
	const UI_CONFIGURATION_MENU_AUTO_GUI = "ui.configuration-menu.auto-gui";
	const UI_CONFIGURATION_MENU_MYPLOT_SUPPORT = "ui.configuration-menu.myplot-support";
	const UI_CONFIGURATION_MENU_AUTO_RELOAD = "ui.configuration-menu.auto-reload";
	const UI_TREE_MENU_TITLE = "ui.tree-menu.title";
	const UI_TREE_MENU_TRUNK_HEIGHT = "ui.tree-menu.trunk-height";
	const UI_TREE_MENU_TRUNK_WIDTH = "ui.tree-menu.trunk-width";
	const UI_TREE_MENU_MAX_BRANCH_LENGTH = "ui.tree-menu.max-branch-length";
	const UI_TREE_MENU_TRUNK_BLOCKS = "ui.tree-menu.trunk-blocks";
	const UI_TREE_MENU_LEAVES_BLOCKS = "ui.tree-menu.leaves-blocks";
	const UI_TREE_MENU_LEAVES_CLUSTER_SIZE = "ui.tree-menu.leaves-cluster-size";
	const LOG_LANGUAGE_AUTO_SELECTED = "log.language.auto-selected";
	const LOG_LANGUAGE_USAGE = "log.language.usage";
	const LOG_LANGUAGE_SELECTED = "log.language.selected";
	const LOG_BRUSH_RESTORED = "log.brush.restored";
	const LOG_BRUSH_ALL_RESTORED = "log.brush.all-restored";
	const LOG_PRESETS_LOADED = "log.presets.loaded";
	const LOG_PRESETS_ALL_LOADED = "log.presets.all-loaded";
	const LOG_RELOAD_START = "log.reload.start";
	const LOG_RELOAD_FINISH = "log.reload.finish";
	const BRUSH_SHAPE_CUBE = "brush.shape.cube";
	const BRUSH_SHAPE_CUBOID = "brush.shape.cuboid";
	const BRUSH_SHAPE_SPHERE = "brush.shape.sphere";
	const BRUSH_SHAPE_CYLINDER = "brush.shape.cylinder";
	const BRUSH_TYPE_BIOME = "brush.type.biome";
	const BRUSH_TYPE_CLEANENTITIES = "brush.type.cleanentities";
	const BRUSH_TYPE_CLEAN = "brush.type.clean";
	const BRUSH_TYPE_DRAIN = "brush.type.drain";
	const BRUSH_TYPE_EXPAND = "brush.type.expand";
	const BRUSH_TYPE_FILL = "brush.type.fill";
	const BRUSH_TYPE_FLATTENALL = "brush.type.flattenall";
	const BRUSH_TYPE_FLATTEN = "brush.type.flatten";
	const BRUSH_TYPE_LAYER = "brush.type.layer";
	const BRUSH_TYPE_LEAFBLOWER = "brush.type.leafblower";
	const BRUSH_TYPE_MELT = "brush.type.melt";
	const BRUSH_TYPE_OVERLAY = "brush.type.overlay";
	const BRUSH_TYPE_REPLACEALL = "brush.type.replaceall";
	const BRUSH_TYPE_REPLACE = "brush.type.replace";
	const BRUSH_TYPE_SNOWCONE = "brush.type.snowcone";
	const BRUSH_TYPE_TOPLAYER = "brush.type.toplayer";
	const BRUSH_TYPE_TREE = "brush.type.tree";

	/** @var string[] */
	private static $translations = [];
	/** @var array */
	private $messageData = [];

	public function __construct(TranslationData $data){
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
	 * @param string $key
	 * @param array  $params
	 *
	 * @return string
	 */
	public static function get(string $key, array $params = []) : string{
		if(!isset(self::$translations[$key])){
			return "Unknown message: Please remove your language file and let it regenerate";
		}
		if(!empty($params)){
			return vsprintf(self::$translations[$key], $params);
		}

		return self::$translations[$key];
	}
}