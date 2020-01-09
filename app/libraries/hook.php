<?php

/*
           8 8888       .8. `8.`888b                 ,8' d888888o.
           8 8888      .888. `8.`888b               ,8'.`8888:' `88.
           8 8888     :88888. `8.`888b             ,8' 8.`8888.   Y8
           8 8888    . `88888. `8.`888b     .b    ,8'  `8.`8888.
           8 8888   .8. `88888. `8.`888b    88b  ,8'    `8.`8888.
           8 8888  .8`8. `88888. `8.`888b .`888b,8'      `8.`8888.
88.        8 8888 .8' `8. `88888. `8.`888b8.`8888'        `8.`8888.
`88.       8 888'.8'   `8. `88888. `8.`888`8.`88'     8b   `8.`8888.
  `88o.    8 88'.888888888. `88888. `8.`8' `8,`'      `8b.  ;8.`8888
    `Y888888 ' .8'       `8. `88888. `8.`   `8'        `Y8888P ,88P'

    JIGSAW ACADEMY WORKFLOW SYSTEM v2
    ---------------------------------
*/

	 // Prevent exclusive access
	if (!defined("JAWS")) {
	      	header('Location: https://www.jigsawacademy.com');
	   	die();
	}

	// This library allows you to hook in to core functionalities of JAWS
	// Each module / library function can have multiple hooks defined within itself.
	// Each hook can have multiple functions defined to be called in order of priority stack (array index)

	// Hook nomenclature - For a function "function_name()" a pre-execution hook would be "__function_name"
	// And a post-execution hook would be "function_name__"
	// Intermediate execution hooks can be called inside functions in context-specific areas. Nomenclature would be use of a single pre- or post-name underscore (to give idea of location of call point relative to function logic flow) insetad of two underscores.

	// Note : Data passed to the handler functions is BY REFERENCE !! (For Old Implementation)
	// Note : Data passed to the handler function is passed by as chosen by the handler !! (For New Implementation)
	// Note : For new implemetation of handle, all handler signatures must be consitent !!

	// Global hooks and handler stack
	global $jaws_hooks;

	// This function allows you to add a hook in to a specific core function and add a handler function to be executed
	// If a handler function exists at the given priority level, the next lower priority available will be assigned instead
	// Lower number on priorirty index is executed earlier... priority execution is ascending, starting from 0.
	// Default priority is 10. Hook priority 0 - 9 reserved for use by core modules
	function hook($hook, $handler, $priority = 10, $allow_duplicate = false) {

		// Check - Function exists
		if (function_exists($handler) === false) return false;

		// Check - Duplicates
		if ($allow_duplicate == false) {
			if (isset($GLOBALS["jaws_hooks"][$hook])) {
				$index = array_search($handler, $GLOBALS["jaws_hooks"][$hook]);
				if ($index !== false) return false;
			}
		}

		// Proceed
		for ($index = $priority; $index < 30000; $index ++) {
			if (isset($GLOBALS["jaws_hooks"][$hook][$index])) continue;
			else {
				$GLOBALS["jaws_hooks"][$hook][$index] = $handler;
				return $index;
			}
		}

	}

	// This will call all the handlers for a specific hook, in order of priority - OLD
	function handle($hook, &$data) {
		if (isset($GLOBALS["jaws_hooks"][$hook])) {
			$exec = [];
			for($i=0, $count=0; $i<30000, $count<count($GLOBALS["jaws_hooks"][$hook]); $i++) {
				if (isset($GLOBALS["jaws_hooks"][$hook][$i])) {
					$exec[]=$GLOBALS["jaws_hooks"][$hook][$i];
					$count++;
				}
			}

			foreach ($exec as $index => $handler) if (function_exists($handler)) call_user_func($handler, array(&$data), $hook);
		}
	}

	// This will call all the handlers for a specific hook, in order of priority - NEW
	// This implementation allows the hooked function to pass parameters in a strict function signature
	// The first passed parameter to this function is expected to be the hook name
	// Subsequent parameters will be passed to the handler
	// Since the handler functions will have an enforced signature, they are expected to be aware of the hooked function's event signature
	function handle_strict($hook, &...$args) {
		$args []= $hook;
		if (isset($GLOBALS["jaws_hooks"][$hook])) {
			$exec = [];
			for($i=0, $count=0; $i<30000, $count<count($GLOBALS["jaws_hooks"][$hook]); $i++) {
				if (isset($GLOBALS["jaws_hooks"][$hook][$i])) {
					$exec[]=$GLOBALS["jaws_hooks"][$hook][$i];
					$count++;
				}
			}

			foreach ($exec as $index => $handler) if (function_exists($handler)) $handler(...$args);
		}
	}

	// This will remove a given handler function from a specific hook's stack
	// Note: If no handler is specified, the hook will be freed from ALL handlers
	function unhook($hook, $handler = "") {
		if (isset($GLOBALS["jaws_hooks"][$hook])) {
			if (strcmp($handler,"") == 0) unset($GLOBALS["jaws_hooks"][$hook]);
			else {
				$index = array_search($handler, $GLOBALS["jaws_hooks"][$hook]);
				if ($index !== false) unset($GLOBALS["jaws_hooks"][$hook][$index]);
			}
		}
	}

	// Unit-testing & interception by hooking
	// ---------------------------------------------------------------------------------
	// ---------------------------------------------------------------------------------

	abstract class _ {
		private function __construct() {}
		private function __clone() {}
		private function __wakeup() {}

		public static $hooks;

		public static function hook(string $exp, string $handler, int $priority = 10) {

			// Sanitize the hook - post execution by default !
			if (($exp[0] != '_') && ($exp[strlen($exp) - 1] != '_')) $exp .= "_";

			// Any hooks installed for this expression ??
			if (isset(self::$hooks[$exp])) {

				// Copy existing array
				$_hooks = self::$hooks[$exp];
				self::$hooks[$exp] = [];
				$flag = false;
				$pos = 0;

				// Compare each hook with new hook's priority and add appropriatly
				foreach ($_hooks as $_hook) {
					if (!$flag) {
						$_priority = array_keys($hook)[0];
						$_handler = array_values($hook)[0];

						// Found proper posiion for new hook
						if ($_priority > $priority) {
							self::$hooks[$exp] []= [$priority => $handler];
							$flag = true;
						}
						else $pos ++;
					}

					// Add exisitng hook anyways
					self::$hooks[$exp] []= $_hook;
				}

				// Is new hook added ?
				if (!$flag) {
					self::$hooks[$exp] []= [$priority => $handler];
					$pos = count(self::$hooks[$exp]) - 1;
				}

				// Return position of new hook
				return $pos;
			}

			// This is the first hook for this expression
			else self::$hooks[$exp] = [[$priority => $handler]];
		}

		public static function unhook(string $exp, string $handler) {
			trigger_error("Fatal interceptor error - Unhook is not implemented!", E_USER_ERROR);
		}

		public static function __callStatic(string $exp, array $args) {

			// Check if function exists
			if (!function_exists($exp)) {
				activity_create("critical","interceptor","function.404","function",$exp,"","","Function not found - ".$exp."(".implode(", ", $args).")");
				trigger_error("Fatal interceptor error - Function not found - ".$exp, E_USER_ERROR);
			}

			// Pre-execution hooks
			$pre = "_".$exp;
			if (isset(self::$hooks[$pre]) && (count(self::$hooks[$pre]) > 0)) {
				foreach(self::$hooks[$pre] as $hook) {
					$handler = array_values($hook)[0];

					if (!function_exists($handler))	activity_create("critical", "interceptor", "handler.404", "function", $handler,"","","Handler not found - ".$pre." Handling - ".$exp."(".implode(", ", $args).")"." pre-execution");
					else {
						try { _::{$handler}($pre, ...$args); }
						catch (Exception $e) {
							activity_create("critical", "interceptor", "handler.404", "function", $handler,"","","Handler not found - ".$pre." Handling - ".$exp."(".implode(", ", $args).")"." pre-execution");
						}
					}
				}
			}

			// Call actual function
			$res = null;
			try { $res = $exp(...$args); }
			catch (Exception $e) {
				activity_create("critical", "interceptor", "function.error", "function", $exp, "", "", "Function error - ".($e->getMessage())." Executing - ".$exp."(".implode(", ", $args).")");
				trigger_error("Fatal interceptor error - Function error - ".($e->getMessage())." Executing - ".$exp, E_USER_ERROR);
			}

			// Post-execution hooks
			$post = $exp."_";
			if (isset(self::$hooks[$post]) && (count(self::$hooks[$post]) > 0)) {
				foreach(self::$hooks[$post] as $hook) {
					$handler = array_values($hook)[0];

					if (!function_exists($handler))	activity_create("critical", "interceptor", "handler.404", "function", $handler,"","","Handler not found - ".$post." Handling - ".$exp."(".implode(", ", $args).")"." post-execution");
					else {
						try { _::{$handler}($post, $res, ...$args); }
						catch (Exception $e) {
							activity_create("critical", "interceptor", "handler.404", "function", $handler,"","","Handler not found - ".$post." Handling - ".$exp."(".implode(", ", $args).")"." post-execution");
						}
					}
				}
			}

			// All done
			return $res;
		}
	}

?>
