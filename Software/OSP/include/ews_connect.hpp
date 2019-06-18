//
// Created by dsengupt on 11/12/18.
//

#ifndef OSP_CLION_CXX_EWS_CONNECTION_HPP
#define OSP_CLION_CXX_EWS_CONNECTION_HPP

#include <iostream>
#include <Poco/Net/TCPServer.h>
#include <Poco/Net/TCPServerConnection.h>
#include <Poco/Net/StreamSocket.h>
#include <Poco/JSON/Parser.h>
#include <Poco/Net/Net.h>
#include <Poco/Net/TCPServerConnectionFactory.h>
#include <Poco/SharedPtr.h>
#include <iostream>
#include <iomanip>
#include <sstream>
#include <string>
#include <unordered_map>
#include <unordered_set>
#include <vector>
#include <cctype>
#include <functional>
#include <assert.h>
#include <cereal/cereal.hpp>
#include <cereal/types/memory.hpp>
#include <cereal/archives/json.hpp>
#include <cereal/types/base_class.hpp>
#include <cereal/archives/binary.hpp>
#include <cereal/types/vector.hpp>
#include <cereal/types/string.hpp>
#include <cereal/types/polymorphic.hpp>
#include <cereal/archives/json.hpp>
#include <cereal/types/vector.hpp>
#include "osp_parser.hpp"


int isalnumquote(int c){
    if(c == '\"'){
        return c;
    }
    else if(c == ','){
        return c;
    }
    else if (c == '_'){
        return c;
    }
    else if (c == '.'){
        return c;
    }
    else if (c == '-'){
        return c;
    }
    else if (c == '/'){
        return c;
    }
    return isalnum(c);
}

template <class P, class M, class T>
class OSPConnection: public Poco::Net::TCPServerConnection {
public:
    OSPConnection(const Poco::Net::StreamSocket& s, P* parser, M* mha): TCPServerConnection(s) {
        this->parser = parser;
        this->mha = mha;
    }

    void run() {
        Poco::Net::StreamSocket& ss = socket();
        ss.setReceiveTimeout(Poco::Timespan(1,0));
        try {
            char buffer[2048];
            int n = ss.receiveBytes(buffer, sizeof(buffer));
            int k;
            int i = 0;
            bool success = false;
            //std::cout << n << std::endl;
            std::string command_string(buffer,n);


            while (n > 0) {
                try {
                    Poco::JSON::Parser parser;
                    Poco::Dynamic::Var result = parser.parse(command_string);
                   // std::cout << command_string << std::endl;
                    Poco::JSON::Object::Ptr object = result.extract<Poco::JSON::Object::Ptr>();
                    if (object->get("method") == "set") {
                        Poco::JSON::Object::Ptr data = object->getObject("data");
                        Poco::Dynamic::Var left = data->get("left");
                        std::string left_user_data = left.toString();
                        std::cout << "left userdata:" << left_user_data << std::endl;
                        osp_json_parser(left_user_data, 1);

                        Poco::Dynamic::Var right = data->get("right");
                        std::string right_user_data = right.toString();
                        std::cout << "right userdata:" <<  right_user_data << std::endl;
                        osp_json_parser(right_user_data, 2);

                        std::string sendResponse = "success";
                        k = ss.sendBytes(sendResponse.c_str(), (int)sendResponse.size());
                        //std::cout << k << std::endl;
                        n = 0;
                        parser.reset();
                        success = true;

                    } else if (object->get("method") == "get") {
                        //std::cout << "get" << std::endl;
                        std::stringstream os;
                        cereal::JSONOutputArchive archive(os);
                        assert(NUM_CHANNEL == 2);
                        T data[2];
                        mha->get_params(data);
                        T left = data[0];
                        T right = data[1];
                        archive( CEREAL_NVP(left), CEREAL_NVP(right));
                        os << "\n}" << std::endl;
                        std::string str = os.str();
                        str.erase(std::remove(str.begin(), str.end(), '\n'), str.end());
                        str.erase(std::remove(str.begin(), str.end(), ' '), str.end());
                        //std::cout << str << "}" << std::endl;
                        for(i = 0; i < (int)str.size();) {
                            k = ss.sendBytes(&str.c_str()[i], (int)str.size() - i);
                            i = i + k;
                            //std::cout << k << " " << i << std::endl;
                        }
//                        i = i - 8;
//                        int size = (int)str.size() - i;
//                        k = ss.sendBytes(&str.c_str()[i], size);
                        //std::cout << k << std::endl;
                        n = 0;

                        success = true;
                    }

                    command_string = "";

                }
                catch (...){
                    //std::cout << command_string << std::endl;
                    n = ss.receiveBytes(buffer, sizeof(buffer));
                   // std::cout << n << std::endl;
                    std::string temp(buffer, n);
                    command_string += temp;
                }

                //std::cout << "before json parser called"<<command_string << std::endl;
                //int ret = osp_json_parser(command_string);

            }
            if(!success){
                std::string sendResponse = "failure";
                k = ss.sendBytes(sendResponse.c_str(), (int)sendResponse.size());
            }
            ss.close();
        }
        catch (...)
        {
            auto expPtr = std::current_exception();

            try
            {
                if(expPtr) std::rethrow_exception(expPtr);
            }
            catch(const std::exception& e) //it would not work if you pass by value
            {
                std::cout << e.what();
            }
        }
    }


    int osp_json_parser(std::string json_string, int channel)
    {
        T user_data[2];

        json_string.erase(remove_if(json_string.begin(), json_string.end(),
                                    std::not1(std::ptr_fun(::isalnumquote))), json_string.end());

        std::string audio = "wav";
        std::size_t found = json_string.find(audio);
        if(found != std::string::npos){
            json_string.erase(json_string.begin()+(found+3));
            json_string.erase(json_string.begin()+(json_string.find_first_of('/')-1));

        }

        std::istringstream iss(json_string);
        std::vector<std::string> json_string_arr((std::istream_iterator<WordDelimitedBy<'\"'>>(iss)),
                                                 std::istream_iterator<WordDelimitedBy<'\"'>>());


        for (int i=1; i < (int)json_string_arr.size(); i++)
        {
            if (i%2 !=0) {
                json_string_arr[i] = "--" + json_string_arr[i];

            } else{
                if (*(json_string_arr[i].end() - 1) == ',') {
                    json_string_arr[i].erase(json_string_arr[i].end() - 1);
                }
            }
        }

        json_string_arr.insert(json_string_arr.begin(), std::to_string(channel));
        json_string_arr.insert(json_string_arr.begin(), "--channel");
        json_string_arr.insert(json_string_arr.begin(), "Temp");

        std::vector<char *> cstrings;
        for(auto& string : json_string_arr) {

            cstrings.push_back(&string.front());
        }
        mha->get_params(user_data);
        parser->parse((int)cstrings.size(), cstrings.data(), user_data);
        mha->set_params(user_data);
        return 0;
    }

private:
    P* parser;
    M* mha;
};


template <class P, class M, class T>
class TCPServerConnectionFactoryImpl1: public Poco::Net::TCPServerConnectionFactory
    /// This template provides a basic implementation of
    /// TCPServerConnectionFactory.
{
public:
    TCPServerConnectionFactoryImpl1(P* parser, M* mha)
    {
        this->parser = parser;
        this->mha = mha;
    }

    ~TCPServerConnectionFactoryImpl1()
    {
//        delete parser;
//        delete mha;
    }

    Poco::Net::TCPServerConnection* createConnection(const Poco::Net::StreamSocket& socket)
    {
        return new OSPConnection<P,M,T>(socket, parser, mha);
    }

private:
    P* parser;
    M* mha;
};

template <class P, class M, class T>
class ews_connection{

public:
    ews_connection(P* parser, M* mha, Poco::UInt16 socket){
        srv = new Poco::Net::TCPServer(new TCPServerConnectionFactoryImpl1<P, M, T>(parser, mha), socket);
        srv->start();
        std::cout << "TCP Server created" << std::endl;
    }
    ~ews_connection(){
        srv->stop();
        delete srv;
    }

private:
    Poco::Net::TCPServer * srv;

};






#endif //OSP_CLION_CXX_EWS_CONNECTION_HPP
