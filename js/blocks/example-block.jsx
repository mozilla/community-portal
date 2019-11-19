export default function exampleBlock() {

  const { registerBlockType } = wp.blocks;
  const { InspectorControls } = wp.blockEditor;
  const { TextControl } = wp.components;

  const blockSlug = 'block';

  registerBlockType(`moz/${blockSlug}`), {
    title: 'Example Block',
    description: 'An example block',
    category: 'common',
    icon: 'star-filled',
  }
  edit: (props) => {
    return (
      <h1>Test Component</h1>
    )
  }
  save: (props) => {
    return (
      <h1>Test Component</h1>
    )
  }
}