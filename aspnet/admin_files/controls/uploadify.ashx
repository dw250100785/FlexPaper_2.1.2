﻿<%@ WebHandler Language="C#" Class="uploadify" %>
using System;
using System.Web;
public class uploadify : IHttpHandler
{
    protected System.Web.UI.HtmlControls.HtmlInputFile filMyFile;
    protected lib.Config configManager;
    
    public void ProcessRequest(HttpContext context)
    {
        configManager = new lib.Config(context.Server.MapPath(VirtualPathUtility.GetDirectory(context.Request.Path)+@"..\..\"));
        
        if (null != context.Request.Files["Filedata"])
        {
            HttpPostedFile myFile = context.Request.Files["Filedata"];
            int nFileLen = myFile.ContentLength;
            byte[] myData = new byte[nFileLen];
            myFile.InputStream.Read(myData, 0, nFileLen);
            System.IO.FileStream newFile = new System.IO.FileStream(configManager.getConfig("path.pdf") + myFile.FileName, System.IO.FileMode.Create);
            newFile.Write(myData, 0, myData.Length);
            newFile.Close();
        }
    }

    public bool IsReusable
    {
        get
        {
            return false;
        }
    }
}