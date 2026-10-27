import java.nio.file.*;
import java.nio.charset.*;
import java.io.*;
import java.net.*;
import java.util.*;
import java.util.stream.*;
import java.util.jar.*;
import com.google.gson.*;
import org.sonar.api.batch.rule.ActiveRule;
import org.sonar.api.rule.RuleKey;
import org.sonarsource.sonarlint.core.analysis.api.*;
import org.sonarsource.sonarlint.core.analysis.container.global.GlobalAnalysisContainer;
import org.sonarsource.sonarlint.core.plugin.commons.*;
import org.sonarsource.sonarlint.core.commons.api.SonarLanguage;
import org.sonarsource.sonarlint.core.commons.progress.ProgressIndicator;

public class RunSonar {
  static class Input implements ClientInputFile {
    final Path base, file;
    Input(Path b, Path f) { base=b; file=f; }
    public String getPath(){return file.toString();}
    public boolean isTest(){return false;}
    public Charset getCharset(){return StandardCharsets.UTF_8;}
    public <G> G getClientObject(){return null;}
    public InputStream inputStream() throws IOException{return Files.newInputStream(file);}
    public String contents() throws IOException{return Files.readString(file);}
    public String relativePath(){return base.relativize(file).toString().replace('\\','/');}
    public URI uri(){return file.toUri();}
    public SonarLanguage language(){return file.toString().endsWith(".css")?SonarLanguage.CSS:(file.toString().endsWith(".php")?SonarLanguage.PHP:SonarLanguage.HTML);}
  }
  record Rule(RuleKey ruleKey, String severity, String language, Map<String,String> params) implements ActiveRule {
    public String param(String k){return params.get(k);}
    public String internalKey(){return null;}
    public String templateRuleKey(){return null;}
    public String qpKey(){return null;}
  }
  public static void main(String[] args) throws Exception {
    Path ext=Path.of(args[0]);
    Path base=Path.of(args[1]).toAbsolutePath();
    Path work=Path.of(args[2]).toAbsolutePath(); Files.createDirectories(work);
    Set<Path> jars=new HashSet<>();
    List<ActiveRule> rules=new ArrayList<>();
    for(String[] spec:List.of(new String[]{"sonarphp.jar","org/sonar/l10n/php/rules/php/","php","php"},new String[]{"sonarhtml.jar","org/sonar/l10n/web/rules/Web/","Web","web"})){
      Path jar=ext.resolve("analyzers").resolve(spec[0]);jars.add(jar);
      try(JarFile archive=new JarFile(jar.toFile())){
        JsonObject profile=JsonParser.parseReader(new InputStreamReader(archive.getInputStream(archive.getJarEntry(spec[1]+"Sonar_way_profile.json")),StandardCharsets.UTF_8)).getAsJsonObject();
        for(JsonElement key:profile.getAsJsonArray("ruleKeys")){
          String name=key.getAsString();
          var entry=archive.getJarEntry(spec[1]+name+".json");
          String severity="MAJOR";
          if(entry!=null){var meta=JsonParser.parseReader(new InputStreamReader(archive.getInputStream(entry),StandardCharsets.UTF_8)).getAsJsonObject();if(meta.has("defaultSeverity"))severity=meta.get("defaultSeverity").getAsString().toUpperCase();}
          rules.add(new Rule(RuleKey.of(spec[2],name),severity,spec[3],Map.of()));
        }
      }
    }
    Path cssJar=ext.resolve("analyzers/sonarjs.jar");jars.add(cssJar);
    try(var cssLoader=new URLClassLoader(new URL[]{cssJar.toUri().toURL()},RunSonar.class.getClassLoader())){
      var cssRules=(List<?>)cssLoader.loadClass("org.sonar.css.CssRules").getMethod("getDefaultQualityProfileRuleKeys",String.class).invoke(null,"Sonar way");
      for(var key:cssRules)rules.add(new Rule(RuleKey.of("css",key.toString()),"MAJOR","css",Map.of()));
    }
    var loaded=new PluginsLoader().load(new PluginsLoader.Configuration(jars,Set.of(SonarLanguage.PHP,SonarLanguage.HTML,SonarLanguage.CSS),false,Optional.empty()),Set.of()).getLoadedPlugins();
    List<ClientInputFile> inputs;
    try(var stream=Files.walk(base)) { inputs=stream.filter(Files::isRegularFile).filter(p->p.toString().endsWith(".php")||p.toString().endsWith(".html")||p.toString().endsWith(".css")).map(p->(ClientInputFile)new Input(base,p)).toList(); }
    var scheduler=AnalysisSchedulerConfiguration.builder().setWorkDir(work).setExtraProperties(Map.of("sonar.js.internal.bundlePath",ext.resolve("eslint-bridge").toString())).setNodeJs(Path.of("C:/Program Files/nodejs/node.exe")).setClientPid(ProcessHandle.current().pid()).setFileSystemProvider(key->new ClientModuleFileSystem(){
      public Stream<ClientInputFile> files(){return inputs.stream();}
      public Stream<ClientInputFile> files(String language,org.sonar.api.batch.fs.InputFile.Type type){return inputs.stream().filter(f->language==null||f.language().getSonarLanguageKey().equals(language));}
    }).build();
    var global=new GlobalAnalysisContainer(scheduler,loaded);
    List<Map<String,Object>> issues=new ArrayList<>();
    try{
      global.startComponents();
      var module=global.getModuleRegistry().getContainerFor("arrue");
      var config=AnalysisConfiguration.builder().setBaseDir(base).addInputFiles(inputs).addActiveRules(rules).build();
      var result=module.analyze(config,issue->{
        Map<String,Object> item=new LinkedHashMap<>();item.put("file",issue.getInputFile()==null?null:issue.getInputFile().relativePath());item.put("rule",issue.getRuleKey().toString());item.put("message",issue.getMessage());item.put("line",issue.getTextRange()==null?null:issue.getTextRange().getStartLine());issues.add(item);
      },new ProgressIndicator(){public void notifyProgress(String s,Integer n){} public boolean isCanceled(){return false;}},org.sonarsource.sonarlint.core.commons.tracing.Trace.begin("local-analysis","arrue"));
      if(!result.failedAnalysisFiles().isEmpty())throw new IllegalStateException("Failed analysis files: "+result.failedAnalysisFiles());
      Files.writeString(work.resolve("issues.json"),new GsonBuilder().setPrettyPrinting().create().toJson(issues));
      System.out.println("SONAR_RESULT="+issues.size()+" files="+inputs.size()+" rules="+rules.size());
    } finally {global.stopComponents();loaded.close();}
  }
}
