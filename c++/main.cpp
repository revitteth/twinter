#include <iostream>
#include <fstream>
#include <boost/thread.hpp>

#include "BufferedAsyncSerial.h"

using namespace std;
using namespace boost;

int main(int argc, char* argv[])
{
	string line;

	try {
        BufferedAsyncSerial serial("/dev/ttyUSB0",9600);
		if(argc >= 1) {
			ifstream inputfile (argv[1]);
			if (inputfile.is_open())
			{
				while ( inputfile.good() )
				{
					getline (inputfile,line);
					serial.writeString(line += '\n');
				}
				inputfile.close();
			}
	
			serial.writeString("\n\n\n\n\n");
			if(argc = 3) {
				if(strcmp(argv[2], "cut") == 0){
					serial.writeString("\x1b\x69");
				} else {
					// do nothing	
				}
			}
		}

        //Simulate doing something else while the serial device replies.
        //When the serial device replies, the second thread stores the received
        //data in a buffer.
        //this_thread::sleep(posix_time::seconds(2));

        //Always returns immediately. If the terminator \r\n has not yet
        //arrived, returns an empty string.
        //cout<<serial.readStringUntil("\r\n")<<endl;

        serial.close();
  
    } catch(boost::system::system_error& e)
    {
        cout<<"Error: "<<e.what()<<endl;
        return 1;
    }
}
