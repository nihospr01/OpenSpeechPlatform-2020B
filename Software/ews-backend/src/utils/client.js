const net = require("net");

var client;

const creactConnection = async (res) => {
  client = net.createConnection({ port: 8001 }, () => {
    // return {
    //   error:false,
    //   msg:"Connection opens!"
    // }
    res.status(200).send("Connection opens!");
  });

  client.on("error", function (err) {
    client.end();
    // return {
    //   error:true,
    //   msg:"Connection fails!"
    // }
    res.status(200).send("Connection fails!");
  });
  return;
};

const endConnection = async () => {
  client.end();

  // client.on("error", function (err) {
  //   console.log(err);
  //   client.end();
  // });
  // return {
  //   error:true,
  //   msg:"Connection fails!"
  // }
};

const sendData = async (res, data) => {
  // client.on("connect", function (data) {
  //   console.log("CONNECT");
  //   console.log(data);
  //   //
  // });

  console.log(client);
  client.write(JSON.stringify(data));

  client.on("data", function (data) {
    console.log(data)
    //res.status(200).send(data);
    client.end();
  });

  client.on("error", function (err) {
    console.log(err);
    // res
    //   .status(500)
    //   .send("get time fails");
    client.end();
    return;
  });

};

module.exports = {
  creactConnection,
  endConnection,
  sendData,
};
