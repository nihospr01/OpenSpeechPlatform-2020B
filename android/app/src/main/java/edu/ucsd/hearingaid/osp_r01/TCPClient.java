package edu.ucsd.hearingaid.osp_r01;

import java.io.DataInputStream;
import java.io.DataOutputStream;
import java.io.IOException;
import java.net.Socket;

public class TCPClient {
    
    private int mPort;
    private Socket mSocket;
    //private ByteArrayOutputStream mByteOut;
    private DataOutputStream mOutputStream;
	private DataInputStream mInputStream;
 
    public TCPClient(int port) {
    	mPort = port;
    }
 
    public void sendMessage(byte arr[]) {
    	try {
			mOutputStream.write(arr, 0, arr.length);
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
    }

	public void getMessage(byte arr[]) {
		try {
			mInputStream.read(arr);
		} catch (IOException e) {
			e.printStackTrace();
		}

		System.out.println("Read " + arr.length + " bytes");
	}

	public int getUnderruns() {
		int underruns = 0;

		try {
			underruns = mInputStream.readInt();
		} catch (IOException e) {
			e.printStackTrace();
		}

		return underruns;
	}
 
    public void stopClient() {
       	if (mSocket != null) {
       		try {
        		mSocket.close();
       		} catch (IOException e) {
       			System.out.println("Failed to close socket");
       			e.printStackTrace();
       		}
       	}
    }
    
    public void connect(String serverAddr) {
    	try {
			mSocket = new Socket(serverAddr, mPort);
			mOutputStream = new DataOutputStream(mSocket.getOutputStream());
			mInputStream = new DataInputStream(mSocket.getInputStream());
		} catch (IOException e) {
			System.out.println("Failed to connect to socket");
			e.printStackTrace();
		}
    }
    
    public boolean isConnected() {
    	return (mSocket != null && mSocket.isConnected());
    }
}
