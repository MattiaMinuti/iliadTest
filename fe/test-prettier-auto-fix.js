// Test file for Prettier auto-fix
const testObject = {
  name: 'test',
  value: 123,
  items: [1, 2, 3, 4, 5],
  nested: {
    prop1: 'value1',
    prop2: 'value2',
  },
};

function testFunction(param1, param2, param3) {
  console.log('Test function', param1, param2, param3);
  return {
    result: 'success',
    data: [1, 2, 3, 4, 5],
  };
}

export default testObject;
