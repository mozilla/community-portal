function exampleBlock() {

  const { registerBlockType } = wp.blocks;
  const { InspectorControls } = wp.blockEditor;
  const { TextControl } = wp.components;
  const { createElement } = wp.element;

  const blockSlug = 'block';

  registerBlockType(`moz/${blockSlug}`, {
    title: 'Example Block',
    description: 'An example block',
    category: 'common',
    icon: 'star-filled',
    edit: function(props) {
      return createElement(
      'h2',
      null,
      'Test Component'
      )
    },
    save: function(props) {
      return null
    },
  })
}

exampleBlock();