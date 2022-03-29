import { registerBlockType } from '@wordpress/blocks';

import json from '../block.json';
import edit from './edit';
import save from './save';

const { name } = json;

// register the video block type
registerBlockType(name, {
  edit,
  save,
});
